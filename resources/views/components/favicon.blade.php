@props([
    'host' => '',
    'size' => 34,
    'radius' => 9,
])

@php
    $clean = preg_replace('#^https?://#', '', (string) $host);
    $letter = $clean !== '' ? strtoupper(mb_substr($clean, 0, 1)) : '?';

    $hash = 0;
    $len = strlen((string) $host);
    for ($i = 0; $i < $len; $i++) {
        $hash = ($hash * 31 + ord($host[$i])) % 360;
    }
    $color = "hsl({$hash} 62% 55%)";
@endphp

<span
    {{ $attributes }}
    style="
        width: {{ $size }}px;
        height: {{ $size }}px;
        border-radius: {{ $radius }}px;
        flex: 0 0 auto;
        display: grid;
        place-items: center;
        color: #fff;
        font-weight: 600;
        font-size: {{ $size * 0.44 }}px;
        background: {{ $color }};
        letter-spacing: 0;
    "
>{{ $letter }}</span>
