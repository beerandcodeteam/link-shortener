<div class="card" style="width: 100%; max-width: 440px; padding: 32px">
    <div style="margin-bottom: 24px">
        <h1 class="h2">Forgot your password?</h1>
        <p class="small">Enter your email and we'll send you a reset link.</p>
    </div>

    @if ($status)
        <div class="hint-text" style="margin-bottom: 16px; color: var(--green)">{{ $status }}</div>
    @endif

    <form wire:submit="sendResetLink" style="display: flex; flex-direction: column; gap: 16px">
        <x-input
            type="email"
            label="Email"
            wire:model="email"
            :error="$errors->first('email')"
            autocomplete="email"
            autofocus
        />

        <x-button type="submit" variant="primary" wire:loading.attr="disabled">
            Email reset link
        </x-button>
    </form>

    <p class="small" style="margin-top: 20px; text-align: center">
        <a href="{{ route('login') }}" wire:navigate>Back to log in</a>
    </p>
</div>
