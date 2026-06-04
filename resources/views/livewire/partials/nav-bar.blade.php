@php
    $url = function (string $name, string $fallback = '#'): string {
        return \Illuminate\Support\Facades\Route::has($name) ? route($name) : $fallback;
    };
@endphp

<nav class="nav sticky top-0 z-50 h-[52px] bg-white/72 backdrop-blur-[20px] backdrop-saturate-[180%] border-b border-[var(--color-line-soft)]" style="box-shadow: var(--shadow-nav);">
    <div class="max-w-[var(--container-max)] mx-auto h-full px-7 flex items-center gap-5">
        <a href="{{ $user ? $url('dashboard', '/dashboard') : $url('home', '/') }}" class="inline-flex items-center gap-2 font-semibold text-[19px] tracking-[-0.03em] text-[var(--color-ink)]">
            <x-ui.logo />
        </a>

        @unless($user)
            <div class="hidden sm:flex items-center gap-[26px] ml-2">
                <a href="{{ $url('home', '/') }}#shorten" class="text-[13.5px] text-[var(--color-ink)] opacity-85 hover:opacity-100 transition-opacity">Shorten</a>
                <a href="{{ $url('home', '/') }}#features" class="text-[13.5px] text-[var(--color-ink)] opacity-85 hover:opacity-100 transition-opacity">Features</a>
                <a href="{{ $url('home', '/') }}#pricing" class="text-[13.5px] text-[var(--color-ink)] opacity-85 hover:opacity-100 transition-opacity">Pricing</a>
            </div>
        @else
            <div class="hidden sm:flex items-center gap-[26px] ml-2">
                <a href="{{ $url('dashboard', '/dashboard') }}" class="text-[13.5px] text-[var(--color-ink)] opacity-85 hover:opacity-100 transition-opacity @if(request()->routeIs('dashboard') || request()->routeIs('links.*')) !opacity-100 font-medium @endif">Links</a>
                <a href="{{ $url('dashboard', '/dashboard') }}" class="text-[13.5px] text-[var(--color-ink)] opacity-85 hover:opacity-100 transition-opacity">Analytics</a>
            </div>
        @endunless

        <div class="flex-1"></div>

        @unless($user)
            <div class="flex items-center gap-2">
                <x-ui.button variant="ghost" size="sm" :href="$url('login', '/login')">Log in</x-ui.button>
                <x-ui.button variant="primary" size="sm" :href="$url('register', '/register')">Sign up</x-ui.button>
            </div>
        @else
            <div class="relative flex items-center gap-[14px]" x-data="{ open: false }" @click.outside="open = false">
                <x-ui.button variant="primary" size="sm" :href="$url('dashboard', '/dashboard')" icon="plus">
                    New link
                </x-ui.button>

                <button
                    type="button"
                    @click="open = !open"
                    class="rounded-full border-0 cursor-pointer bg-[var(--color-ink)] text-white text-[12.5px] font-semibold"
                    style="width: 32px; height: 32px; letter-spacing: 0;"
                >
                    {{ collect(explode(' ', $user->name))->map(fn ($w) => strtoupper(substr($w, 0, 1)))->take(2)->implode('') }}
                </button>

                <div
                    x-show="open"
                    x-cloak
                    x-transition.opacity.duration.150ms
                    class="absolute right-0 top-[40px] w-[220px] bg-white rounded-[14px] border border-[var(--color-line-soft)] p-2 z-[100]"
                    style="box-shadow: var(--shadow-pop);"
                >
                    <div class="px-[10px] py-2">
                        <div class="text-[14px] font-semibold">{{ $user->name }}</div>
                        <div class="text-[12.5px] text-[var(--color-ink-3)]">{{ $user->email }}</div>
                    </div>
                    <hr class="divider">
                    <button
                        type="button"
                        wire:click="logout"
                        class="w-full text-left border-0 bg-transparent px-[10px] py-[9px] rounded-lg text-[14px] text-[var(--color-ink)] cursor-pointer hover:bg-[var(--color-bg-soft)]"
                    >
                        Sign out
                    </button>
                </div>
            </div>
        @endunless
    </div>
</nav>
