<div class="w-full max-w-[420px]">
    <div class="rounded-lg bg-white p-8 shadow-sm border border-line-soft">
        <h1 class="text-2xl font-semibold tracking-tight text-ink mb-1">Reset your password</h1>
        <p class="text-sm text-ink-3 mb-6">
            Enter the email address associated with your account and we'll send you a link to reset your password.
        </p>

        @session('status')
            <p class="mb-4 text-sm text-green">{{ $value }}</p>
        @endsession

        <form wire:submit="sendResetLink" class="space-y-4">
            <x-input type="email" label="Email" :error="$errors->first('email')">
                <x-slot name="placeholder">name@example.com</x-slot>
            </x-input>

            <button
                type="submit"
                disabled="{{ $errors->any() ? '' : null }}"
                class="btn btn-primary inline-flex items-center justify-center gap-2 text-sm font-medium h-[40px] px-5 rounded-[980px] cursor-pointer w-full"
            >
                Send reset link
            </button>
        </form>

        <p class="mt-4 text-sm text-ink-2">
            Remember your password? <a href="{{ route('login') }}" class="text-blue">Log in</a>
        </p>
    </div>
</div>
