{{-- ============================================================
    FaviconChip
    Shows the first letter of a URL's host inside a colored chip.
    The background color is derived from a deterministic hash
    of the hostname so the same host always gets the same hue.

    Props:
        host   — full URL or hostname (default "?")
        size   — width/height in px (default 34)
        radius — border-radius in px (default 9)

    Example:
        <x-favicon-chip host="https://github.com/Lucas" />
    ============================================================ !!}

@props(['host' => '?', 'size' => 34, 'radius' => 9])

<?php
// Deterministic hue from hostname (mirrors faviconColor in ui.jsx)
$h = 0;
$clean = str_replace(['https://', 'http://'], '', (string) $host);
for ($i = 0, $len = strlen($clean); $i < $len; $i++) {
    $h = ($h * 31 + ord($clean[$i])) % 360;
}
$firstChar = strtoupper($clean[0] ?? '');
$letter = '' === $firstChar ? '?' : $firstChar;
?>

<span style="
    width: {{ $size }}px;
    height: {{ $size }}px;
    border-radius: {{ $radius }}px;
    flex: 0 0 auto;
    display: grid;
    place-items: center;
    color: #fff;
    font-weight: 600;
    font-size: {{ max(1, (int)($size * 0.44)) }}px;
    background: hsl({{ $h }}% 62% 55%);
    letter-spacing: 0;
" class="inline-block">{{ $letter }}</span>
