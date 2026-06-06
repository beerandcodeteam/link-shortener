@props([
    'variant' => 'primary',
    'size' => null,
    'href' => null,
    'type' => 'button',
    'icon' => null,
])

@php
    $classes = collect(['btn', 'btn-' . $variant])
        ->when($size === 'sm', fn ($c) => $c->push('btn-sm'))
        ->when($size === 'lg', fn ($c) => $c->push('btn-lg'))
        ->implode(' ');
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @isset($icon)
            <x-icon :name="$icon" :size="$size === 'sm' ? 16 : 18" />
        @endisset
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        @isset($icon)
            <x-icon :name="$icon" :size="$size === 'sm' ? 16 : 18" />
        @endisset
        {{ $slot }}
    </button>
@endif
