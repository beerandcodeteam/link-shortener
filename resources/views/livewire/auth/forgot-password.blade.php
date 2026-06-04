<div class="wrap-narrow py-12 sm:py-16">
    <div class="mx-auto w-full max-w-[400px]">
        <h1 class="h1">Reset your password</h1>
        <p class="body mt-2 mb-7">Enter the email associated with your account and we'll send a reset link.</p>

        @if ($statusMessage)
            <div class="rounded-[var(--radius-md)] border border-[var(--color-line)] bg-[var(--color-bg-soft)] p-3 text-[14px] text-[var(--color-ink-2)]">
                {{ $statusMessage }}
            </div>
        @endif

        <form wire:submit="sendLink" class="mt-4 flex flex-col gap-4" novalidate>
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

            <x-ui.button type="submit" variant="primary" size="lg" class="mt-2 justify-center">
                Email reset link
            </x-ui.button>
        </form>

        <p class="small mt-6 text-center text-[var(--color-ink-3)]">
            Remembered it?
            <a href="{{ route('login') }}" class="text-[var(--color-blue)] hover:underline">Back to log in</a>
        </p>
    </div>
</div>
