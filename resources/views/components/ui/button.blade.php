@props([
    'variant' => 'primary', // primary | ghost | soft | danger
    'size' => 'default',    // default | sm | lg | icon
    'type' => 'button',
    'href' => null,
    'icon' => null,
    'as' => null,
])

@php
    $base = 'inline-flex items-center justify-center gap-2 border-0 font-medium leading-none whitespace-nowrap transition-[background,transform,color,border-color] duration-150 active:scale-[.97]';

    $variants = [
        'primary' => 'bg-[var(--color-blue)] text-white hover:bg-[var(--color-blue-press)]',
        'ghost'   => 'bg-transparent text-[var(--color-blue)] hover:bg-[var(--color-blue-tint)]',
        'soft'    => 'bg-[var(--color-bg-soft)] text-[var(--color-ink)] hover:bg-[#ececef]',
        'danger'  => 'bg-transparent text-[var(--color-red)] hover:bg-[var(--color-red-tint)]',
    ];

    $sizes = [
        'default' => 'rounded-[var(--radius-pill)] text-[15px] px-5 py-[11px]',
        'sm'      => 'rounded-[var(--radius-pill)] text-[13.5px] px-[14px] py-2',
        'lg'      => 'rounded-[var(--radius-pill)] text-[17px] px-7 py-[14px]',
        'icon'    => 'w-9 h-9 p-0 rounded-full bg-transparent text-[var(--color-ink-2)] hover:bg-[var(--color-bg-soft)] hover:text-[var(--color-ink)]',
    ];

    $classes = trim($base.' '.$variants[$variant].' '.$sizes[$size]);

    $tag = $as ?? ($href ? 'a' : 'button');
@endphp

<{{ $tag }}
    @if($tag === 'button') type="{{ $type }}" @endif
    @if($href) href="{{ $href }}" @endif
    {{ $attributes->merge(['class' => $classes]) }}
>
    @if($icon)
        <x-ui.icon :name="$icon" :size="$size === 'lg' ? 18 : 16" />
    @endif
    {{ $slot }}
</{{ $tag }}>
