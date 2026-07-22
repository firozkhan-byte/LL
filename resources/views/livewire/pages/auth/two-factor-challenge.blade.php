<?php

use App\Services\UserService;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $code = '';
    public string $recovery_code = '';
    public bool $useRecoveryCode = false;
    public ?string $error = null;

    /**
     * Mount the component and verify pre-authentication state.
     */
    public function mount(): void
    {
        if (!Session::has('auth.2fa.user_id')) {
            $this->redirect(route('login', absolute: false), navigate: true);
        }
    }

    /**
     * Verify the 2FA code.
     */
    public function verify(UserService $userService): void
    {
        $userId = Session::get('auth.2fa.user_id');
        $user = User::find($userId);

        if (!$user) {
            $this->redirect(route('login', absolute: false), navigate: true);
            return;
        }

        if ($this->useRecoveryCode) {
            $this->validate([
                'recovery_code' => ['required', 'string'],
            ]);

            $recoveryCodes = json_decode(decrypt($user->two_factor_recovery_codes), true) ?? [];
            
            if (($key = array_search($this->recovery_code, $recoveryCodes)) !== false) {
                // Remove used recovery code
                unset($recoveryCodes[$key]);
                $user->update([
                    'two_factor_recovery_codes' => encrypt(json_encode(array_values($recoveryCodes))),
                ]);

                $this->completeLogin($user);
                return;
            }

            $this->error = 'The provided recovery code was invalid.';
        } else {
            $this->validate([
                'code' => ['required', 'string', 'size:6'],
            ]);

            $secret = decrypt($user->two_factor_secret);

            if ($userService->verifyTwoFactorCode($secret, $this->code)) {
                $this->completeLogin($user);
                return;
            }

            $this->error = 'The verification code was invalid. Please try again.';
        }
    }

    /**
     * Complete authentication.
     */
    protected function completeLogin(User $user): void
    {
        $remember = Session::get('auth.2fa.remember', false);
        
        Auth::login($user, $remember);
        
        Session::forget(['auth.2fa.user_id', 'auth.2fa.remember']);
        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
        @if (!$useRecoveryCode)
            {{ __('Please confirm access to your account by entering the authentication code provided by your authenticator application.') }}
        @else
            {{ __('Please confirm access to your account by entering one of your emergency recovery codes.') }}
        @endif
    </div>

    <form wire:submit="verify">
        @if (!$useRecoveryCode)
            <div>
                <x-input-label for="code" :value="__('Code')" />
                <x-text-input wire:model="code" id="code" class="block mt-1 w-full" type="text" inputmode="numeric" autofocus autocomplete="one-time-code" />
                <x-input-error :messages="$errors->get('code')" class="mt-2" />
            </div>
        @else
            <div>
                <x-input-label for="recovery_code" :value="__('Recovery Code')" />
                <x-text-input wire:model="recovery_code" id="recovery_code" class="block mt-1 w-full" type="text" autofocus autocomplete="one-time-code" />
                <x-input-error :messages="$errors->get('recovery_code')" class="mt-2" />
            </div>
        @endif

        @if ($error)
            <div class="mt-2 text-sm text-red-600 dark:text-red-400">
                {{ $error }}
            </div>
        @endif

        <div class="flex items-center justify-end mt-4">
            <button type="button" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 underline cursor-pointer" wire:click="$toggle('useRecoveryCode')">
                @if (!$useRecoveryCode)
                    {{ __('Use a recovery code') }}
                @else
                    {{ __('Use an authentication code') }}
                @endif
            </button>

            <x-primary-button class="ms-4">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</div>
