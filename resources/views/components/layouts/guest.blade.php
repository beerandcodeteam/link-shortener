@props([
    'title' => null,
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name', 'Snip') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @livewireStyles
    </head>
    <body>
        <x-nav-bar :user="null" />

        <main class="wrap" style="display: flex; flex-direction: column; align-items: center; padding-top: 64px; padding-bottom: 96px; min-height: calc(100vh - 52px)">
            {{ $slot }}
        </main>

        <footer class="footer">
            <div class="wrap" style="display: flex; align-items: center; justify-content: space-between; gap: 16px; padding-top: 24px; padding-bottom: 24px">
                <span>&copy; {{ date('Y') }} Snip. Built for the web.</span>
                <span style="display: flex; gap: 20px">
                    <a href="{{ url('/#features') }}">Features</a>
                    <a href="{{ url('/#pricing') }}">Pricing</a>
                </span>
            </div>
        </footer>

        <x-toast />

        @livewireScripts
    </body>
</html>
