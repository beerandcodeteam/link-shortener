<div class="wrap-narrow py-12 sm:py-16">
    <div class="mx-auto w-full max-w-[400px]">
        <h1 class="h1">Create your account</h1>
        <p class="body mt-2 mb-7">Free forever for personal links.</p>

        <form wire:submit="register" class="flex flex-col gap-4" novalidate>
            <x-ui.input
                type="text"
                name="name"
                label="Name"
                placeholder="Alex Rivera"
                required
                autofocus
                autocomplete="name"
                wire:model="name"
                :error="$errors->first('name')"
            />

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
                label="Password"
                placeholder="At least 8 characters"
                required
                autocomplete="new-password"
                wire:model="password"
                :error="$errors->first('password')"
            />

            <x-ui.input
                type="password"
                name="password_confirmation"
                label="Confirm password"
                placeholder="Repeat your password"
                required
                autocomplete="new-password"
                wire:model="password_confirmation"
                :error="$errors->first('password_confirmation')"
            />

            <x-ui.button type="submit" variant="primary" size="lg" class="mt-2 justify-center">
                Create account
            </x-ui.button>
        </form>

        <p class="small mt-6 text-center text-[var(--color-ink-3)]">
            Already have an account?
            <a href="{{ route('login') }}" class="text-[var(--color-blue)] hover:underline">Log in</a>
        </p>
    </div>
</div>
