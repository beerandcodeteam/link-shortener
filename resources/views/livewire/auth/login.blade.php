<div class="card" style="width: 100%; max-width: 440px; padding: 32px">
    <div style="margin-bottom: 24px">
        <h1 class="h2">Welcome back</h1>
        <p class="small">Log in to manage your links.</p>
    </div>

    <form wire:submit="login" style="display: flex; flex-direction: column; gap: 16px">
        <x-input
            type="email"
            label="Email"
            wire:model="email"
            :error="$errors->first('email')"
            autocomplete="email"
            autofocus
        />

        <x-input
            type="password"
            label="Password"
            wire:model="password"
            :error="$errors->first('password')"
            autocomplete="current-password"
        />

        <div style="display: flex; align-items: center; justify-content: space-between">
            <x-checkbox label="Remember me" wire:model="remember" />
            <a class="small" href="{{ route('password.request') }}" wire:navigate>Forgot password?</a>
        </div>

        <x-button type="submit" variant="primary" wire:loading.attr="disabled">
            Log in
        </x-button>
    </form>

    <p class="small" style="margin-top: 20px; text-align: center">
        Don't have an account?
        <a href="{{ route('register') }}" wire:navigate>Sign up</a>
    </p>
</div>
