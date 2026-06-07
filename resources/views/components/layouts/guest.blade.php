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
        @include('components.nav-bar', ['mode' => 'guest'])

        {{-- Centered content area (mirrors ui.jsx screen layout) --}}
        <div class="flex flex-1 items-center justify-center w-full transition-opacity opacity-100 duration-750 lg:grow">
            <main>
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
