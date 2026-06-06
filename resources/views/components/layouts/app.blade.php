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
        <x-nav-bar />

        <main class="wrap" style="padding-top: 40px; padding-bottom: 96px; min-height: calc(100vh - 52px)">
            {{ $slot }}
        </main>

        <x-toast />

        @livewireScripts
    </body>
</html>
