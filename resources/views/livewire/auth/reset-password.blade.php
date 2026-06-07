<div class="w-full max-w-[420px]">
    <div class="rounded-lg bg-white p-8 shadow-sm border border-line-soft">
        <h1 class="text-2xl font-semibold tracking-tight text-ink mb-1">Set a new password</h1>
        <p class="text-sm text-ink-3 mb-6">
            Enter your email and a new password below.
        </p>

        @if ($errors->hasAny(['email']))
            <div class="mb-4 rounded-md bg-red/5 p-4 border border-red">
                <p class="text-sm text-red">{{ $errors->first('email') }}</p>
            </div>
        @endif

        <form wire:submit="resetPassword" class="space-y-4">
            <x-input type="email" label="Email" :error="$errors->first('email')">
                <x-slot name="placeholder">name@example.com</x-slot>
            </x-input>

            <div class="space-y-1.5">
                <label for="password" class="block text-xs font-medium text-ink-2 mb-1.5 select-none">Password</label>
                <input
                    id="password"
                    wire:model.live="password"
                    type="password"
                    placeholder="At least 8 characters"
                    class="{{ $errors->has('password') ? '!border-red focus:!ring-red-tint' : 'border-line transition-border duration-150 focus:border-blue focus:ring-4 focus:ring-blue-tint/50' }} block w-full rounded-md border py-3.5 px-4 text-ink placeholder:ink-3 bg-white"
                />
                @error('password')
                    <span class="block mt-1 text-sm text-red">{{ $message }}</span>
                @enderror
            </div>

            <x-input type="password" label="Confirm password" :error="$errors->first('password_confirmation')">
                <x-slot name="placeholder">Repeat your password</x-slot>
            </x-input>

            <input type="hidden" wire:model="token" />

            <button
                type="submit"
                disabled="{{ $errors->any() ? '' : null }}"
                class="btn btn-primary inline-flex items-center justify-center gap-2 text-sm font-medium h-[40px] px-5 rounded-[980px] cursor-pointer w-full"
            >
                Set new password
            </button>
        </form>
    </div>
</div>
