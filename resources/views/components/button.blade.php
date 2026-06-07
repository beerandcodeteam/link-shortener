{{-- ============================================================
    Button
    Variants — primary, ghost, soft, danger.
    Sizes   — default (h-[35px]) / sm  (h-[28px]).
    Pill radius by default. Icon slot supported.
   ============================================================ --}}

@props([
    'variant' => 'primary',  // primary | ghost | soft | danger
    'size'    => 'default',  // default | sm
])

@php
$base = 'inline-flex items-center justify-center gap-2 font-medium cursor-pointer transition-all';

$_classes = match ($variant) {
    'ghost'  => 'text-sm py-1.5 px-4 text-blue hover:bg-blue-tint rounded-[980px]',
    'soft'   => 'text-sm py-1.5 px-5 bg-blue/10 text-blue hover:bg-blue/15 rounded-[980px]',
    'danger' => 'text-sm py-1.5 px-4 text-red hover:bg-red-tint border border-transparent hover:border-red/20 rounded-[980px]',
    default  => 'h-[35px] px-5 rounded-[980px] bg-blue text-white hover:bg-blue/90 text-sm',
};

$_classes .= match ($size) {
    'sm'    => ' h-[28px] px-3 text-xs',
    default => '',
};
@endphp

<button {{ $attributes->merge(['type' => 'button', 'class' => $base . ' ' . $_classes]) }}>
    @if (isset($icon))
        <span class="flex-shrink-0">{{ $icon }}</span>
    @endif
    {{ $slot }}
</button>
