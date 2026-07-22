<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserService
{
    protected UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getPaginatedUsers(array $filters = [], int $perPage = 10)
    {
        return $this->userRepository->getPaginated($filters, $perPage);
    }

    public function findUser(string $id): ?User
    {
        return $this->userRepository->find($id);
    }

    public function createUser(array $data): User
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            $data['password'] = Hash::make(Str::random(16)); // secure default
        }

        $roles = $data['roles'] ?? [];
        unset($data['roles']);

        $user = $this->userRepository->create($data);

        if (! empty($roles)) {
            $user->syncRoles($roles);
        }

        return $user;
    }

    public function updateUser(string $id, array $data): ?User
    {
        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $roles = $data['roles'] ?? null;
        if (isset($data['roles'])) {
            unset($data['roles']);
        }

        $user = $this->userRepository->update($id, $data);

        if ($user && $roles !== null) {
            $user->syncRoles($roles);
        }

        return $user;
    }

    public function deleteUser(string $id): bool
    {
        return $this->userRepository->delete($id);
    }

    public function restoreUser(string $id): bool
    {
        return $this->userRepository->restore($id);
    }

    public function updateAvatar(string $id, UploadedFile $file): ?User
    {
        $user = $this->userRepository->find($id);
        if (! $user) {
            return null;
        }

        // Delete old avatar if exists
        if ($user->avatar_path) {
            Storage::disk('public')->delete($user->avatar_path);
        }

        $path = $file->store('avatars', 'public');

        return $this->userRepository->update($id, ['avatar_path' => $path]);
    }

    public function removeAvatar(string $id): bool
    {
        $user = $this->userRepository->find($id);
        if (! $user || ! $user->avatar_path) {
            return false;
        }

        Storage::disk('public')->delete($user->avatar_path);
        $this->userRepository->update($id, ['avatar_path' => null]);

        return true;
    }

    public function generateTwoFactorSecret(string $id): array
    {
        $user = $this->userRepository->find($id);
        if (! $user) {
            return [];
        }

        // Generate base32 secret (16 chars)
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret = '';
        for ($i = 0; $i < 16; $i++) {
            $secret .= $chars[rand(0, 31)];
        }

        // Let's generate recovery codes
        $recoveryCodes = [];
        for ($i = 0; $i < 8; $i++) {
            $recoveryCodes[] = Str::random(10).'-'.Str::random(10);
        }

        $this->userRepository->update($id, [
            'two_factor_secret' => encrypt($secret),
            'two_factor_recovery_codes' => encrypt(json_encode($recoveryCodes)),
        ]);

        return [
            'secret' => $secret,
            'recovery_codes' => $recoveryCodes,
            'qr_code_url' => $this->getTwoFactorQrCodeUrl($user->email, $secret),
        ];
    }

    public function confirmTwoFactor(string $id, string $code): bool
    {
        $user = $this->userRepository->find($id);
        if (! $user || ! $user->two_factor_secret) {
            return false;
        }

        $secret = decrypt($user->two_factor_secret);

        if ($this->verifyTwoFactorCode($secret, $code)) {
            $this->userRepository->update($id, [
                'two_factor_confirmed_at' => now(),
            ]);

            return true;
        }

        return false;
    }

    public function disableTwoFactor(string $id): bool
    {
        $this->userRepository->update($id, [
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ]);

        return true;
    }

    protected function getTwoFactorQrCodeUrl(string $email, string $secret): string
    {
        $company = rawurlencode(config('app.name', 'ERP'));
        $email = rawurlencode($email);

        return "otpauth://totp/{$company}:{$email}?secret={$secret}&issuer={$company}&algorithm=SHA1&digits=6&period=30";
    }

    public function verifyTwoFactorCode(string $secret, string $code): bool
    {
        $timeSlice = floor(time() / 30);

        for ($i = -1; $i <= 1; $i++) {
            if ($this->calculateTotp($secret, $timeSlice + $i) === $code) {
                return true;
            }
        }

        // For testing/mocking, let's accept "000000" as a bypass to make developer life easier in local dev
        if (config('app.env') === 'local' && $code === '000000') {
            return true;
        }

        return false;
    }

    protected function calculateTotp(string $secret, int $timeSlice): string
    {
        $secretKey = $this->base32Decode($secret);
        $time = pack('N*', 0).pack('N*', $timeSlice);
        $hmac = hash_hmac('sha1', $time, $secretKey, true);
        $offset = ord(substr($hmac, -1)) & 0x0F;
        $hashpart = substr($hmac, $offset, 4);
        $value = unpack('N', $hashpart);
        $value = $value[1];
        $value = $value & 0x7FFFFFFF;
        $modulo = pow(10, 6);

        return str_pad((string) ($value % $modulo), 6, '0', STR_PAD_LEFT);
    }

    protected function base32Decode(string $base32): string
    {
        $base32 = strtoupper($base32);
        if (! preg_match('/^[A-Z2-7=]+$/', $base32)) {
            return '';
        }

        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $buffer = 0;
        $bufferSize = 0;
        $decoded = '';

        for ($i = 0; $i < strlen($base32); $i++) {
            $char = $base32[$i];
            if ($char === '=') {
                break;
            }

            $val = strpos($chars, $char);
            $buffer = ($buffer << 5) | $val;
            $bufferSize += 5;

            if ($bufferSize >= 8) {
                $bufferSize -= 8;
                $decoded .= chr(($buffer >> $bufferSize) & 0xFF);
            }
        }

        return $decoded;
    }
}
