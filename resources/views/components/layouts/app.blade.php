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
        @include('components.nav-bar', ['mode' => 'app'])

        {{-- Main content --}}
        <main class="flex flex-1 w-full px-7 py-[28px] mx-auto max-w-[1080px]">
            {{ $slot }}
        </main>
    </body>
</html>
