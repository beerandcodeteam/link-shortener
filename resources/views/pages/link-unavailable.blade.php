<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Link Unavailable — Link Shortener</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased text-[color:var(--ink)] bg-[color:var(--bg)]">
    <div class="mx-auto max-w-lg px-6 py-24 sm:py-32 lg:px-8">
        <div class="text-center">
            <p class="text-sm font-semibold uppercase tracking-wide text-[color:var(--amber)]">Link Unavailable</p>
            <h1 class="mt-4 text-5xl font-bold tracking-tight sm:text-6xl">This link is no longer active.</h1>
            <p class="mt-6 text-lg leading-8 text-[color:var(--ink-2)]">
                The short URL you're trying to follow has been disabled by its owner.
            </p>
            <div class="mt-10 flex items-center justify-center gap-4">
                <a href="/" class="rounded-full px-6 py-3 text-sm font-semibold bg-[color:var(--blue)] text-white hover:bg-[color:var(--blue-press)] transition-colors">
                    Go Home
                </a>
            </div>
        </div>
    </div>
</body>
</html>
