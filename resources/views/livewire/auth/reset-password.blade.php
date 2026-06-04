<div class="wrap-narrow py-12 sm:py-16">
    <div class="mx-auto w-full max-w-[400px]">
        <h1 class="h1">Set a new password</h1>
        <p class="body mt-2 mb-7">Choose a new password to regain access to your account.</p>

        <form wire:submit="resetPassword" class="flex flex-col gap-4" novalidate>
            <x-ui.input
                type="email"
                name="email"
                label="Email"
                placeholder="you@example.com"
                required
                autocomplete="email"
                wire:model="email"
                :error="$errors->first('email')"
            />

            <x-ui.input
                type="password"
                name="password"
                label="New password"
                placeholder="At least 8 characters"
                required
                autocomplete="new-password"
                wire:model="password"
                :error="$errors->first('password')"
            />

            <x-ui.input
                type="password"
                name="password_confirmation"
                label="Confirm new password"
                placeholder="Repeat your password"
                required
                autocomplete="new-password"
                wire:model="password_confirmation"
                :error="$errors->first('password_confirmation')"
            />

            <x-ui.button type="submit" variant="primary" size="lg" class="mt-2 justify-center">
                Reset password
            </x-ui.button>
        </form>
    </div>
</div>
