<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Link unavailable &middot; {{ config('app.name', 'Laravel') }}</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="min-h-screen flex items-center justify-center bg-neutral-50 text-neutral-900 antialiased p-6">
    <main class="max-w-md w-full text-center">
        <div class="mx-auto mb-6 h-12 w-12 rounded-full bg-neutral-200 flex items-center justify-center text-neutral-500">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-6 w-6" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.181 8.68a4 4 0 0 1 1.414 5.657l-1.414 1.414a4 4 0 0 1-5.657-5.657l.707-.707"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.819 15.32a4 4 0 0 1-1.414-5.657l1.414-1.414a4 4 0 0 1 5.657 5.657l-.707.707"/>
            </svg>
        </div>
        <h1 class="text-2xl font-semibold tracking-tight">This link is no longer available</h1>
        <p class="mt-3 text-neutral-600">
            The short link <span class="font-mono text-neutral-800">/{{ $link->short_code }}</span> has been disabled by its owner and is not redirecting anywhere.
        </p>
        <a href="{{ route('home') }}" class="mt-8 inline-flex items-center justify-center rounded-full bg-neutral-900 px-5 py-2.5 text-sm font-medium text-white hover:bg-neutral-800">
            Go to homepage
        </a>
    </main>
</body>
</html>
