<div class="card" style="width: 100%; max-width: 440px; padding: 32px">
    <div style="margin-bottom: 24px">
        <h1 class="h2">Create your account</h1>
        <p class="small">Save and manage your short links.</p>
    </div>

    <form wire:submit="register" style="display: flex; flex-direction: column; gap: 16px">
        <x-input
            label="Name"
            wire:model="name"
            :error="$errors->first('name')"
            autocomplete="name"
            autofocus
        />

        <x-input
            type="email"
            label="Email"
            wire:model="email"
            :error="$errors->first('email')"
            autocomplete="email"
        />

        <x-input
            type="password"
            label="Password"
            wire:model="password"
            :error="$errors->first('password')"
            hint="At least 8 characters."
            autocomplete="new-password"
        />

        <x-input
            type="password"
            label="Confirm password"
            wire:model="password_confirmation"
            autocomplete="new-password"
        />

        <x-button type="submit" variant="primary" wire:loading.attr="disabled">
            Create account
        </x-button>
    </form>

    <p class="small" style="margin-top: 20px; text-align: center">
        Already have an account?
        <a href="{{ route('login') }}" wire:navigate>Log in</a>
    </p>
</div>
