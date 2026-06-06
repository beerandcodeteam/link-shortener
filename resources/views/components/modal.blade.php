@props([
    'name',
    'width' => '460px',
    'show' => false,
])

{{--
    Mirrors ui.jsx <Modal>: scrim, centered card, ESC-to-close, click-outside close.
    Open/close via Alpine window events:
        $dispatch('open-modal', '{{ $name }}')
        $dispatch('close-modal', '{{ $name }}')
--}}
<div
    x-data="{ open: @js($show) }"
    x-on:open-modal.window="$event.detail === '{{ $name }}' && (open = true)"
    x-on:close-modal.window="$event.detail === '{{ $name }}' && (open = false)"
    x-on:keydown.escape.window="open = false"
    x-show="open"
    x-cloak
    class="scrim"
    @mousedown="open = false"
    {{ $attributes }}
>
    <div class="modal" style="max-width: {{ $width }}" @mousedown.stop>
        {{ $slot }}
    </div>
</div>
