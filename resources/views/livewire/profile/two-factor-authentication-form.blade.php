<?php

use App\Services\UserService;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component
{
    public bool $showingQrCode = false;
    public bool $showingRecoveryCodes = false;
    public string $code = '';
    public string $secret = '';
    public string $qrCodeUrl = '';
    public array $recoveryCodes = [];
    public ?string $error = null;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $user = Auth::user();
        if ($user->two_factor_confirmed_at) {
            $this->recoveryCodes = json_decode(decrypt($user->two_factor_recovery_codes), true) ?? [];
        }
    }

    /**
     * Enable 2FA: Generate secret and show QR code.
     */
    public function enableTwoFactor(UserService $userService): void
    {
        $user = Auth::user();
        $data = $userService->generateTwoFactorSecret($user->id);

        $this->secret = $data['secret'];
        $this->recoveryCodes = $data['recovery_codes'];
        $this->qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($data['qr_code_url']);
        
        $this->showingQrCode = true;
        $this->error = null;
        $this->code = '';
    }

    /**
     * Confirm 2FA: Verify the code entered by the user.
     */
    public function confirmTwoFactor(UserService $userService): void
    {
        $this->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $user = Auth::user();
        
        if ($userService->confirmTwoFactor($user->id, $this->code)) {
            $this->showingQrCode = false;
            $this->showingRecoveryCodes = true;
            $this->error = null;
            $this->dispatch('status-message', 'Two-Factor Authentication has been enabled!');
        } else {
            $this->error = 'Invalid verification code. Please try again.';
        }
    }

    /**
     * Disable 2FA.
     */
    public function disableTwoFactor(UserService $userService): void
    {
        $user = Auth::user();
        $userService->disableTwoFactor($user->id);
        
        $this->showingQrCode = false;
        $this->showingRecoveryCodes = false;
        $this->secret = '';
        $this->qrCodeUrl = '';
        $this->recoveryCodes = [];
        $this->error = null;
        
        $this->dispatch('status-message', 'Two-Factor Authentication has been disabled.');
    }

    /**
     * Cancel the enabling process.
     */
    public function cancelEnabling(UserService $userService): void
    {
        $user = Auth::user();
        $userService->disableTwoFactor($user->id);
        
        $this->showingQrCode = false;
        $this->secret = '';
        $this->qrCodeUrl = '';
        $this->recoveryCodes = [];
        $this->error = null;
    }
}; ?>

<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Two Factor Authentication') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Add additional security to your account using two factor authentication.') }}
        </p>
    </header>

    @if (auth()->user()->two_factor_confirmed_at)
        <div class="p-4 bg-green-50 dark:bg-green-900/30 text-green-800 dark:text-green-300 rounded-lg text-sm">
            {{ __('You have enabled two factor authentication. Your account is now more secure.') }}
        </div>

        @if ($showingRecoveryCodes)
            <div class="mt-4 p-4 bg-gray-100 dark:bg-gray-700/50 rounded-lg text-sm">
                <p class="font-semibold text-gray-800 dark:text-gray-200">
                    {{ __('Store these recovery codes in a secure password manager. They can be used to recover access to your account if your two factor authentication device is lost.') }}
                </p>
                <div class="grid grid-cols-2 gap-2 mt-4 font-mono text-xs text-gray-600 dark:text-gray-400">
                    @foreach ($recoveryCodes as $rCode)
                        <div>{{ $rCode }}</div>
                    @endforeach
                </div>
                <button type="button" wire:click="$set('showingRecoveryCodes', false)" class="mt-4 text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                    {{ __('Hide Recovery Codes') }}
                </button>
            </div>
        @else
            <div class="flex items-center space-x-4">
                <button type="button" wire:click="$set('showingRecoveryCodes', true)" class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                    {{ __('Show Recovery Codes') }}
                </button>
            </div>
        @endif

        <div class="mt-6">
            <x-danger-button wire:click="disableTwoFactor">
                {{ __('Disable Two-Factor Authentication') }}
            </x-danger-button>
        </div>
    @elseif ($showingQrCode)
        <div class="mt-4 space-y-4">
            <p class="text-sm text-gray-600 dark:text-gray-400">
                {{ __('To finish enabling two factor authentication, scan the following QR code using your phone\'s authenticator application (e.g., Google Authenticator, Authy), then enter the 6-digit confirmation code below.') }}
            </p>

            <div class="flex justify-center p-4 bg-white rounded-lg inline-block shadow max-w-[240px]">
                <img src="{{ $qrCodeUrl }}" alt="QR Code" />
            </div>

            <div class="text-sm font-mono bg-gray-100 dark:bg-gray-900 p-3 rounded text-gray-800 dark:text-gray-200">
                <strong>{{ __('Setup Key:') }}</strong> {{ $secret }}
            </div>

            <div class="max-w-xl">
                <x-input-label for="code" :value="__('Verification Code')" />
                <x-text-input wire:model="code" id="code" type="text" class="mt-1 block w-2/3" placeholder="000000" maxlength="6" required autocomplete="one-time-code" autofocus />
                <x-input-error class="mt-2" :messages="$errors->get('code')" />
                @if ($error)
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $error }}</p>
                @endif
            </div>

            <div class="flex items-center space-x-3 mt-4">
                <x-primary-button wire:click="confirmTwoFactor">
                    {{ __('Confirm & Enable') }}
                </x-primary-button>
                <x-secondary-button wire:click="cancelEnabling">
                    {{ __('Cancel') }}
                </x-secondary-button>
            </div>
        </div>
    @else
        <div class="mt-6">
            <x-primary-button wire:click="enableTwoFactor">
                {{ __('Enable Two-Factor Authentication') }}
            </x-primary-button>
        </div>
    @endif
</section>
