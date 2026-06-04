<div class="wrap-narrow py-12 sm:py-16">
    <div class="mx-auto w-full max-w-[400px]">
        <h1 class="h1">Welcome back</h1>
        <p class="body mt-2 mb-7">Log in to your dashboard.</p>

        <form wire:submit="login" class="flex flex-col gap-4" novalidate>
            <x-ui.input
                type="email"
                name="email"
                label="Email"
                placeholder="you@example.com"
                required
                autofocus
                autocomplete="email"
                wire:model="email"
                :error="$errors->first('email')"
            />

            <x-ui.input
                type="password"
                name="password"
                label="Password"
                placeholder="Your password"
                required
                autocomplete="current-password"
                wire:model="password"
                :error="$errors->first('password')"
            />

            <label class="flex items-center gap-2 text-[14px] text-[var(--color-ink-2)]">
                <input
                    type="checkbox"
                    wire:model="remember"
                    class="rounded border-[var(--color-line)] text-[var(--color-blue)] focus:ring-[var(--color-blue)]"
                >
                Remember me
            </label>

            <x-ui.button type="submit" variant="primary" size="lg" class="mt-2 justify-center">
                Log in
            </x-ui.button>
        </form>

        <p class="small mt-4 text-center text-[var(--color-ink-3)]">
            <a href="{{ route('password.request') }}" class="hover:underline">Forgot your password?</a>
        </p>

        <p class="small mt-2 text-center text-[var(--color-ink-3)]">
            New to {{ config('app.name', 'Snip') }}?
            <a href="{{ route('register') }}" class="text-[var(--color-blue)] hover:underline">Create an account</a>
        </p>
    </div>
</div>
