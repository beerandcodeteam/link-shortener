<div class="card" style="width: 100%; max-width: 440px; padding: 32px">
    <div style="margin-bottom: 24px">
        <h1 class="h2">Reset your password</h1>
        <p class="small">Choose a new password for your account.</p>
    </div>

    <form wire:submit="resetPassword" style="display: flex; flex-direction: column; gap: 16px">
        <x-input
            type="email"
            label="Email"
            wire:model="email"
            :error="$errors->first('email')"
            autocomplete="email"
        />

        <x-input
            type="password"
            label="New password"
            wire:model="password"
            :error="$errors->first('password')"
            hint="At least 8 characters."
            autocomplete="new-password"
            autofocus
        />

        <x-input
            type="password"
            label="Confirm new password"
            wire:model="password_confirmation"
            autocomplete="new-password"
        />

        <x-button type="submit" variant="primary" wire:loading.attr="disabled">
            Reset password
        </x-button>
    </form>
</div>
