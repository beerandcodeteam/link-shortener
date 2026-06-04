@props([
    'host' => '',
    'size' => 34,
    'radius' => 9,
])

@php
    $colors = [
        '#0071e3', '#1d9d57', '#b25e00', '#d7373f', '#6e4cff',
        '#0091ae', '#c2185b', '#5e35b1', '#00897b', '#f4511e',
    ];
    $seed = 0;
    for ($i = 0; $i < strlen($host); $i++) { $seed = ($seed * 31 + ord($host[$i])) & 0xffffffff; }
    $color = $colors[abs($seed) % count($colors)];

    $clean = preg_replace('#^https?://#', '', $host ?? '');
    $letter = strtoupper(substr($clean ?? '?', 0, 1));
    if ($letter === '') $letter = '?';
@endphp

<span
    {{ $attributes->merge(['class' => 'inline-grid place-items-center text-white font-semibold flex-shrink-0']) }}
    style="width: {{ (int) $size }}px; height: {{ (int) $size }}px; border-radius: {{ (int) $radius }}px; background: {{ $color }}; font-size: {{ (int) round($size * 0.44) }}px; letter-spacing: 0;"
>{{ $letter }}</span>
