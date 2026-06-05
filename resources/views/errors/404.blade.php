<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Page not found &middot; {{ config('app.name', 'Snip') }}</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="min-h-screen flex items-center justify-center bg-[var(--color-bg)] text-[var(--color-ink)] antialiased p-6" data-testid="error-404">
    <main class="max-w-md w-full text-center fade-up">
        <div class="mx-auto mb-7 h-14 w-14 rounded-[var(--radius-md)] bg-[var(--color-bg-soft)] flex items-center justify-center text-[var(--color-ink-2)]">
            <x-ui.icon name="close" :size="26" />
        </div>
        <p class="eyebrow" data-testid="error-404-code">Error 404</p>
        <h1 class="h2 mt-2" data-testid="error-404-title">We couldn't find that page</h1>
        <p class="body mt-3">
            The short link
            <span class="mono text-[var(--color-ink)] break-all">/{{ request()->route('shortCode') ?? request()->path() }}</span>
            doesn't exist &mdash; it may have never been created, or the code was typed incorrectly.
        </p>
        <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
            <x-ui.button variant="primary" :href="route('home')">Go to homepage</x-ui.button>
            @auth
                <x-ui.button variant="soft" :href="route('dashboard')">Open dashboard</x-ui.button>
            @endauth
        </div>
    </main>
</body>
</html>
