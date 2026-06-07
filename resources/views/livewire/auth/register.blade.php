<div class="w-full max-w-[420px]">
    <div class="rounded-lg bg-white p-8 shadow-sm border border-line-soft">
        <h1 class="text-2xl font-semibold tracking-tight text-ink mb-1">Create your account</h1>
        <p class="text-sm text-ink-3 mb-6">
            Already have an account? {{ link_to_route('login', 'Log in') }}
        </p>

        <form wire:submit="register" class="space-y-4">
            {{-- Name --}}
            <x-input label="Name" :error="$errors->first('name')">
                <x-slot name="placeholder">Your full name</x-slot>
            </x-input>

            {{-- Email --}}
            <x-input type="email" label="Email" :error="$errors->first('email')">
                <x-slot name="placeholder">name@example.com</x-slot>
            </x-input>

            {{-- Password --}}
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

            {{-- Confirm Password --}}
            <x-input type="password" label="Confirm password" :error="$errors->first('password_confirmation')">
                <x-slot name="placeholder">Repeat your password</x-slot>
            </x-input>

            <button
                type="submit"
                disabled="{{ $processing ? '' : null }}"
                class="btn btn-primary inline-flex items-center justify-center gap-2 text-sm font-medium h-[40px] px-5 rounded-[980px] cursor-pointer w-full {{ $errors->hasAny(['name', 'email']) ? '!border-red' : '' }}"
            >
                Create account

                <span wire:loading class="ml-1 animate-spin">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><path d="M12 2a10 10 0 0 1 10 10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                </span>
            </button>
        </form>

        @if (session()->has('message'))
            <p class="mt-4 text-sm text-green">{{ session('message') }}</p>
        @endif
    </div>
</div>
