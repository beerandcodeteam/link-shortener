@props([
    'name',
    'size' => 20,
    'stroke' => 1.7,
])

@php
    $paths = [
        'link' => '<path d="M9 15l6-6"/><path d="M11 6l1-1a4.5 4.5 0 0 1 6.4 6.4l-1 1"/><path d="M13 18l-1 1A4.5 4.5 0 0 1 5.6 12.6l1-1"/>',
        'arrow' => '<path d="M5 12h14"/><path d="M13 6l6 6-6 6"/>',
        'copy' => '<rect x="9" y="9" width="11" height="11" rx="2.4"/><path d="M5 15V5a2 2 0 0 1 2-2h10"/>',
        'check' => '<path d="M5 12.5l4.2 4.2L19 7"/>',
        'plus' => '<path d="M12 5v14"/><path d="M5 12h14"/>',
        'trash' => '<path d="M4 7h16"/><path d="M9 7V5a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/><path d="M6 7l1 13a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1l1-13"/>',
        'chart' => '<path d="M4 19V5"/><path d="M4 19h16"/><rect x="7" y="11" width="3" height="5" rx="0.6" fill="currentColor" stroke="none"/><rect x="12" y="7" width="3" height="9" rx="0.6" fill="currentColor" stroke="none"/><rect x="17" y="13" width="3" height="3" rx="0.6" fill="currentColor" stroke="none"/>',
        'cursor' => '<path d="M5 3l14 7-6 1.6L9.6 17z"/>',
        'eye' => '<path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="2.6"/>',
        'power' => '<path d="M12 4v8"/><path d="M7.5 7.5a7 7 0 1 0 9 0"/>',
        'chevron' => '<path d="M9 6l6 6-6 6"/>',
        'back' => '<path d="M15 6l-6 6 6 6"/>',
        'x' => '<path d="M6 6l12 12"/><path d="M18 6L6 18"/>',
        'globe' => '<circle cx="12" cy="12" r="9"/><path d="M3 12h18"/><path d="M12 3a14 14 0 0 1 0 18 14 14 0 0 1 0-18z"/>',
        'clock' => '<circle cx="12" cy="12" r="9"/><path d="M12 7.5V12l3 2"/>',
        'qr' => '<rect x="4" y="4" width="6" height="6" rx="1"/><rect x="14" y="4" width="6" height="6" rx="1"/><rect x="4" y="14" width="6" height="6" rx="1"/><path d="M14 14h2v2M20 14v0M16 18v2h-2M20 18v2"/>',
        'bolt' => '<path d="M13 3L5 13h6l-1 8 8-10h-6z"/>',
        'shield' => '<path d="M12 3l8 3v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V6z"/><path d="M9 12l2 2 4-4"/>',
        'spark' => '<path d="M12 3l1.8 5.4L19 10l-5.2 1.6L12 17l-1.8-5.4L5 10l5.2-1.6z"/>',
        'sort' => '<path d="M8 4v16M8 20l-3-3M8 4l3 3"/>',
        'device' => '<rect x="6" y="3" width="12" height="18" rx="2.4"/><path d="M11 18h2"/>',
        'desktop' => '<rect x="3" y="4" width="18" height="12" rx="2"/><path d="M9 20h6M12 16v4"/>',
    ];
@endphp

<svg
    {{ $attributes->merge(['width' => $size, 'height' => $size]) }}
    viewBox="0 0 24 24"
    fill="none"
    stroke="currentColor"
    stroke-width="{{ $stroke }}"
    stroke-linecap="round"
    stroke-linejoin="round"
>
    {!! $paths[$name] ?? '' !!}
</svg>
