@props([
    'text',
    'label' => null,
    'variant' => 'soft',
])

<div
    {{ $attributes }}
    x-data="{
        done: false,
        copy() {
            try { navigator.clipboard?.writeText(@js($text)); } catch (e) {}
            this.done = true;
            clearTimeout(this.timer);
            this.timer = setTimeout(() => this.done = false, 1400);
        },
    }"
    style="display: inline-flex"
>
    @if ($label)
        <button type="button" class="btn btn-{{ $variant }} btn-sm" @click.stop="copy()">
            <template x-if="done"><x-icon name="check" :size="16" /></template>
            <template x-if="!done"><x-icon name="copy" :size="16" /></template>
            <span x-text="done ? 'Copied' : @js($label)"></span>
        </button>
    @else
        <button type="button" class="btn-icon" title="Copy" @click.stop="copy()" :style="done ? 'color: var(--green)' : ''">
            <template x-if="done"><x-icon name="check" :size="18" /></template>
            <template x-if="!done"><x-icon name="copy" :size="18" /></template>
        </button>
    @endif
</div>
