<div class="w-full max-w-[420px]">
    <div class="rounded-lg bg-white p-8 shadow-sm border border-line-soft">
        <h1 class="text-2xl font-semibold tracking-tight text-ink mb-1">Log in to your account</h1>
        <p class="text-sm text-ink-3 mb-6">
            Don't have an account? {{ link_to_route('register', 'Create one') }}
        </p>

        <form wire:submit="login" class="space-y-4">
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
                    placeholder="Your password"
                    class="{{ $errors->has('password') ? '!border-red focus:!ring-red-tint' : 'border-line transition-border duration-150 focus:border-blue focus:ring-4 focus:ring-blue-tint/50' }} block w-full rounded-md border py-3.5 px-4 text-ink placeholder:ink-3 bg-white"
                />
                @error('password')
                    <span class="block mt-1 text-sm text-red">{{ $message }}</span>
                @enderror
            </div>

            {{-- Remember me --}}
            <div class="flex items-center gap-2">
                <input
                    type="checkbox"
                    wire:model.live="remember"
                    id="remember"
                    class="h-4 w-4 rounded border-line text-blue focus:ring-blue"
                />
                <label for="remember" class="text-sm text-ink-2">Remember me</label>
            </div>

            @error('email')
                @unless $errors->has('password')
                    <p class="text-sm text-red">{{ $message }}</p>
                @endunless
            @enderror

            <button
                type="submit"
                disabled="{{ $errors->wasAnyFilled() ? '' : null }}"
                class="btn btn-primary inline-flex items-center justify-center gap-2 text-sm font-medium h-[40px] px-5 rounded-[980px] cursor-pointer w-full {{ $errors->hasAny(['email', 'password']) ? '!border-red' : '' }}"
            >
                Log in
            </button>
        </form>

        @if (session()->has('message'))
            <p class="mt-4 text-sm text-green">{{ session('message') }}</p>
        @endif
    </div>
</div>
