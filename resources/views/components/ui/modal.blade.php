@props([
    'show' => false,
    'onClose' => null,
    'width' => 460,
])

@php
    $uid = 'modal-'.uniqid();
@endphp

<div
    x-data="{ open: @js($show) }"
    x-show="open"
    x-cloak
    @if($onClose)
        wire:poll.500ms="noop"
    @endif
    @keydown.escape.window="open = false; @if($onClose) {{ $onClose }} @endif"
    class="fixed inset-0 z-[150] bg-black/32 grid place-items-center p-6 backdrop-blur-[2px]"
    style="animation: fade .2s ease;"
    @if($onClose)
        wire:click.self="{{ $onClose }}"
    @else
        @click="open = false"
    @endif
>
    <div
        class="bg-white rounded-[var(--radius-lg)] w-full shadow-[var(--shadow-pop)] p-7"
        style="max-width: {{ (int) $width }}px; animation: modal-in .26s cubic-bezier(.2,.8,.2,1);"
        @click.stop
    >
        {{ $slot }}
    </div>
</div>
