<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body>
    <livewire:partials.nav-bar />

    <main class="min-h-[calc(100vh-52px)]">
        {{ $slot }}
    </main>

    <footer class="border-t border-[var(--color-line-soft)] py-8">
        <div class="wrap flex flex-col sm:flex-row items-center justify-between gap-3 text-[13px] text-[var(--color-ink-3)]">
            <span>&copy; {{ date('Y') }} {{ config('app.name', 'Snip') }}</span>
            <div class="flex items-center gap-5">
                <a href="#" class="hover:text-[var(--color-ink)] transition-colors">Privacy</a>
                <a href="#" class="hover:text-[var(--color-ink)] transition-colors">Terms</a>
            </div>
        </div>
    </footer>

    <livewire:partials.toast-region />

    @livewireScripts
</body>
</html>
