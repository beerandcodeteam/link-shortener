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
                <a href="{{ route('dashboard') }}" class="logo inline-flex items-center gap-2 font-semibold text-lg tracking-tight text-ink cursor-pointer" aria-label="Snip home">
                    <span class="logo-mark w-[26px] h-[26px] grid place-items-center">
                        <svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M8.5 3v4a1 1 0 0 1-1 1h-4m13-5h5m-5 14h5m-5 4v3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M21.5 10.5L10.5 21.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </span>
                    Snip
                </a>

                {{-- Authenticated nav links --}}
                <div class="nav-links flex items-center gap-[26px] ml-2">
                    <a href="{{ route('dashboard') }}"
                       class="nav-link text-[13.5px] text-ink/85 hover:text-ink opacity-85 hover:opacity-100 transition-opacity font-medium"
                       style="font-weight: {{ request()->routeIs('dashboard*') ? 500 : 400 }}">
                        Links
                    </a>
                    <a href="#" class="nav-link text-[13.5px] text-ink/85 hover:text-ink opacity-85 hover:opacity-100 transition-opacity"
                       style="font-weight: {{ request()->routeIs('analytics*') ? 500 : 400 }}">
                        Analytics
                    </a>
                </div>

                <div class="nav-spacer flex-1"></div>

                {{-- Right side: "New link" CTA + avatar menu --}}
                <div class="flex items-center gap-2">
                    {{-- New link CTA --}}
                    <button wire:navigate
                            href="{{ route('dashboard.create') }}"
                            title="Create new link"
                            class="btn btn-primary btn-sm inline-flex items-center justify-center gap-2 text-sm font-medium h-[35px] px-[19px] rounded-[980px] cursor-pointer">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M8 1v14M1 8h14"/></svg>
                        New link
                    </button>

                    {{-- Avatar dropdown --}}
                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        <button @click="open = !open" type="button" class="avatar w-8 h-8 rounded-full bg-blue-tint flex items-center justify-center text-blue font-semibold text-sm cursor-pointer hover:border-blue/30 transition-colors focus:outline-none focus:ring-2 focus:ring-blue/50">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </button>

                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 top-full mt-2 w-[180px] bg-bg rounded-lg shadow-pop border border-line-soft py-1 z-50"
                             style="display: none;">
                            <div class="px-4 py-2 text-xs text-ink-3 font-medium">
                                {{ auth()->user()->email }}
                            </div>
                            <div class="border-t border-line-soft my-1"></div>

                            {{-- Sign out (Livewire action) --}}
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-ink-2 hover:text-red hover:bg-red-tint cursor-pointer">
                                    Sign out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        {{-- Main content --}}
        <main class="flex flex-1 w-full px-7 py-[28px] mx-auto max-w-[1080px]">
            {{ $slot }}
        </main>
    </body>
</html>
