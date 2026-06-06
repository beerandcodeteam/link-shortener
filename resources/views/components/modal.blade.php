{{-- ============================================================
    Modal
    Scrim overlay with centered card.  Mirrors ui.jsx Modal —
    ESC-to-close, click-outside close, configurable max-width.

    Props:
        wire → Livewire boolean property to set `false` on close
        width— max-width value (e.g. "480px") or null for 384px default
   ============================================================ !!}}

@props(['wire' => null, 'width' => null])

{{-- Build the close handler based on whether wire prop is set --}}
<?php $close = $wire ? "\$wire.set('{$wire}', false);" : ''; ?>

<div
    x-data="{ open: true }"
    @keydown.escape.window="open=false; {{ $close }}$dispatch('modal:closed')"
    x-show="open"
    x-cloak
    x-trap.inert.nonfocusable="open"
>
{{-- Scrim — fires onClose --}}
<div
    x-show="open"
    @click="open=false; {{ $close }}$dispatch('modal:closed')"
    x-transition:enter="ease-out duration-180"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-[50] grid place-items-center bg-black/40"
></div>

{{-- Card --}}
<div
    x-show="open"
    x-cloak
    @click.stop
    x-transition:enter="ease-out duration-200"
    x-transition:enter-start="opacity-0 scale-[.95] translate-y-4 sm:translate-y-0 sm:scale-100"
    x-transition:enter-end="opacity-100 scale-100 translate-y-0 sm:scale-100"
    x-transition:leave="ease-in duration-150"
    x-transition:leave-start="opacity-100 scale-100 translate-y-0 sm:scale-100"
    x-transition:leave-end="opacity-0 scale-[.95] translate-y-4 sm:translate-y-0 sm:scale-100"
    class="w-full mx-auto px-4"
>
    <div {{ $attributes->merge([
        'class' => 'rounded-lg bg-white shadow-pop p-6',
        'style' => null !== $width ? "max-width:{$width}" : 'max-width:384px',
    ]) }}>
        {{ $slot }}
    </div>
</div>

</div>
