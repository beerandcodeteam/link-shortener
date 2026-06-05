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
        <nav class="border-b border-line-soft bg-white/80 backdrop-blur-[20px] sticky top-0 z-50 h-12 px-6 md:px-8 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="font-semibold text-lg tracking-tight uppercase">Snip</span>
            </div>

            <div class="flex items-center gap-6 ml-2">
                <a href="{{ route('login') }}" class="hover:opacity-100 opacity-80 transition font-medium text-sm">Log in</a>
                <a href="{{ route('register') }}" class="bg-blue px-5 py-1.5 rounded-full text-white hover:bg-blue-press transition duration-200 text-sm shadow-none">Sign up</a>
            </div>
        </nav>

        <main class="flex-grow flex justify-center items-center py-12">
            <div class="max-w-[420px] w-full text-center">
                {{ $slot }}
            </div>
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
