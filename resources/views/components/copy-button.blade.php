{{-- ============================================================
    CopyButton
    Copies text to clipboard via Alpine, shows "Copied" check state.

    Props:
        value — text to copy
        label  — button label (default "Copy")
    Slots:
        default override icon (default: copy-icon)

    Example:
        <x-copy-button value="https://short.link/x9">
            Copy short link
        </x-copy-button>
    ============================================================ !!}

@props(['value', 'label' => 'Copy'])

<div class="inline-flex">
    <button
        type="button"
        x-data="{ copied: false }"
        @click="navigator.clipboard.writeText('{{ $value }}'); copied = true; setTimeout(() => copied = false, 1800)"
        :class="copied
            ? 'gap-1.5 text-[--green]'
            : 'gap-1.5 text-[--ink-3] hover:text-[--ink]'
        "
        class="inline-flex items-center gap-1.5 rounded-lg border border-[--border-subtle] bg-white px-2.5 py-1.5 text-xs font-medium shadow-sm transition-colors cursor-pointer"
    >
        <svg x-show="!copied" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-3.5">
            <rect x="9" y="9" width="13" height="13" rx="2"/>
            <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>
        </svg>

        <svg x-show="copied" x-cloak xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="size-3.5">
            <polyline points="20 6 9 17 4 12"/>
        </svg>

        <span x-show="copied" x-clo>{{ copied ? 'Copied!' : $label }}</span>
    </button>
</div>
