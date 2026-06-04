@props([
    'name' => '?',
    'size' => 32,
    'user' => null,
])

@php
    $source = $user['name'] ?? $user->name ?? $name;
    $parts = preg_split('/\s+/', trim((string) $source));
    $initials = strtoupper(implode('', array_map(fn($w) => substr($w, 0, 1), array_slice($parts ?: ['?'], 0, 2))));
    $fontSize = max(10, (int) round(((int) $size) * 0.39));
@endphp

<span
    {{ $attributes->merge(['class' => 'inline-grid place-items-center rounded-full bg-[var(--color-ink)] text-white font-semibold select-none']) }}
    style="width: {{ (int) $size }}px; height: {{ (int) $size }}px; font-size: {{ $fontSize }}px; letter-spacing: 0;"
>{{ $initials }}</span>
