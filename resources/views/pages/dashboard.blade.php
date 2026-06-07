<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ __('Dashboard') }} — {{ config('app.name') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="bg-bg text-ink antialiased min-h-screen flex flex-col">
        {{-- Sticky glassmorphic nav --}}
        @include('components.nav-bar', ['mode' => 'app'])

        <div class="flex flex-1 items-start justify-center w-full transition-opacity opacity-100 duration-750 lg:grow py-8">
            <main class="w-full max-w-[1080px] px-7">
                <x-toast />

                <h1 class="text-2xl font-semibold tracking-tight text-ink mb-6">{{ __('Dashboard') }}</h1>

                {{-- Placeholder for future LinkList component (Phase 6.2) --}}
                <div class="rounded-lg bg-white border border-line-soft p-8 text-center">
                    <p class="text-ink-3">Your links will appear here.</p>
                </div>
            </main>
        </div>

        @livewireScripts
    </body>
</html>
