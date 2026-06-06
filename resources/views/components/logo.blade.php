@props([
    'size' => 19,
])

@php
    $mark = $size * 1.35;
@endphp

<span {{ $attributes->merge(['class' => 'logo']) }} style="font-size: {{ $size }}px">
    <span class="logo-mark" style="width: {{ $mark }}px; height: {{ $mark }}px">
        <svg width="{{ $mark }}" height="{{ $mark }}" viewBox="0 0 28 28" fill="none">
            <rect width="28" height="28" rx="7.5" fill="#1d1d1f" />
            <path d="M11 17l6-6" stroke="#fff" stroke-width="1.9" stroke-linecap="round" />
            <path d="M12.6 8.4l1-1a3.4 3.4 0 0 1 4.8 4.8l-1 1" stroke="#fff" stroke-width="1.9" stroke-linecap="round" />
            <path d="M15.4 19.6l-1 1a3.4 3.4 0 0 1-4.8-4.8l1-1" stroke="#fff" stroke-width="1.9" stroke-linecap="round" />
        </svg>
    </span>
    Snip
</span>
