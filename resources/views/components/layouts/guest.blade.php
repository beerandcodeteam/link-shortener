<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ $title ?? config('app.name') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="bg-bg text-ink antialiased min-h-screen flex flex-col">
        {{-- Sticky glassmorphic nav --}}
        <nav class="nav sticky top-0 z-50 h-[52px] bg-white/72 backdrop-blur-xl border-b border-line-soft">
            <div class="nav-inner max-w-[1080px] mx-auto h-full px-7 flex items-center gap-5">
                {{-- Logo --}}
                <a href="{{ route('home') }}" class="logo inline-flex items-center gap-2 font-semibold text-lg tracking-tight text-ink cursor-pointer" aria-label="Snip home">
                    <span class="logo-mark w-[26px] h-[26px] grid place-items-center">
                        <svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M8.5 3v4a1 1 0 0 1-1 1h-4m13-5h5m-5 14h5m-5 4v3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M21.5 10.5L10.5 21.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </span>
                    Snip
                </a>

                {{-- Auth-state-aware links --}}
                <div class="nav-links flex items-center gap-[26px] ml-2">
                    @auth
                        <a href="{{ route('dashboard') }}"
                           class="nav-link text-[13.5px] text-ink/85 hover:text-ink opacity-85 hover:opacity-100 transition-opacity font-medium"
                           style="font-weight: {{ request()->routeIs('dashboard') || request()->routeIs('link.detail*') ? 500 : 400 }}">
                            Links
                        </a>
                        <a href="#" class="nav-link text-[13.5px] text-ink/85 hover:text-ink opacity-85 hover:opacity-100 transition-opacity">Analytics</a>
                    @else
                        <a href="{{ route('home') }}" class="nav-link text-[13.5px] text-ink/85 hover:text-ink opacity-85 hover:opacity-100 transition-opacity">Shorten</a>
                        <a href="#" class="nav-link text-[13.5px] text-ink/85 hover:text-ink opacity-85 hover:opacity-100 transition-opacity">Features</a>
                        <a href="#" class="nav-link text-[13.5px] text-ink/85 hover:text-ink opacity-85 hover:opacity-100 transition-opacity">Pricing</a>
                    @endauth
                </div>

                <div class="nav-spacer flex-1"></div>

                {{-- Auth buttons --}}
                <div class="flex items-center gap-2">
                    @auth
                        <button wire:click="{{ auth()->id() }}" title="Create new link" class="btn btn-primary btn-sm inline-flex items-center justify-center gap-2 text-sm font-medium h-[35px] px-[19px] rounded-[980px] cursor-pointer">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M8 1v14M1 8h14"/></svg>
                            New link
                        </button>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-ghost btn-sm inline-flex items-center justify-center gap-2 text-sm font-medium h-[35px] px-[14px] rounded-[980px] cursor-pointer">Log in</a>
                        <a href="{{ route('register') }}" class="btn btn-primary btn-sm inline-flex items-center justify-center gap-2 text-sm font-medium h-[35px] px-[19px] rounded-[980px] cursor-pointer">Sign up</a>
                    @endauth
                </div>
            </div>
        </nav>

        {{-- Centered content area (mirrors ui.jsx screen layout) --}}
        <div class="flex flex-1 items-center justify-center w-full transition-opacity opacity-100 duration-750 lg:grow">
            <main {{ $attributes }}>
                {{ $slot }}
            </main>
        </div>

        {{-- Footer --}}
        <footer class="border-t border-line-soft mt-[40px] pt-[28px] pb-[28px] bg-bg-softer">
        <div class="max-w-[1080px] mx-auto px-7 flex items-center justify-between flex-wrap gap-3">
            <span class="text-[19px] font-semibold tracking-tight text-ink">Snip</span>
            <p class="m-0 text-ink-3">A prototype — Snip keeps every link tidy.</p>
        </div>
    </footer>
    </body>
</html>
