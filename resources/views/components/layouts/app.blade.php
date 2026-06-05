<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Link Shortener') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
</head>
<body class="antialiased bg-white">
    <div class="min-h-screen flex flex-col">
        <!-- Navigation -->
        @include('components.layouts.navbar')

        <main class="flex-1 max-w-[1080px] mx-auto w-full px-6 md:px-8 py-8">
            {{ $slot }}
        </main>

        <footer class="py-8 border-t border-line-soft text-center">
            <div class="text-sm text-ink-3">
                &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </div>
        </footer>
    </div>

    @livewireScripts
</body>
</html>
