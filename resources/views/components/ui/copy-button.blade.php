@props([
    'text' => '',
    'label' => null,
    'variant' => 'soft',
    'icon' => true,
])

@php
    $uid = 'copy-'.uniqid();
@endphp

<div
    x-data="{ done: false }"
    {{ $attributes->merge(['class' => 'inline-flex']) }}
>
    <button
        type="button"
        @click="
            navigator.clipboard?.writeText(@js($text));
            done = true;
            setTimeout(() => done = false, 1400);
            $event.stopPropagation();
        "
        @if($label)
            class="btn btn-{{ $variant }} btn-sm inline-flex items-center justify-center gap-2 border-0 rounded-[var(--radius-pill)] text-[13.5px] px-[14px] py-2 font-medium transition-[background,transform,color,border-color] active:scale-[.97]"
            style="background: var(--color-bg-soft); color: var(--color-ink);"
        >
            <span x-show="!done" class="inline-flex">
                <x-ui.icon name="copy" :size="16" />
            </span>
            <span x-show="done" x-cloak class="inline-flex text-[var(--color-green)]">
                <x-ui.icon name="check" :size="16" />
            </span>
            <span x-text="done ? 'Copied' : @js($label)"></span>
        </button>
    @else
        <button
            type="button"
            title="Copy"
            @click="
                navigator.clipboard?.writeText(@js($text));
                done = true;
                setTimeout(() => done = false, 1400);
                $event.stopPropagation();
            "
            class="w-9 h-9 p-0 rounded-full bg-transparent text-[var(--color-ink-2)] inline-grid place-items-center transition-colors hover:bg-[var(--color-bg-soft)] hover:text-[var(--color-ink)]"
            :class="done ? '!text-[var(--color-green)]' : ''"
        >
            <span x-show="!done"><x-ui.icon name="copy" :size="18" /></span>
            <span x-show="done" x-cloak><x-ui.icon name="check" :size="18" /></span>
        </button>
    @endif
</div>
