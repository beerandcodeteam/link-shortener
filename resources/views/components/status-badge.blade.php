{{-- ============================================================
    StatusBadge
    Pill badge showing link status — active (green) or disabled (neutral).
    Mirrors ui.jsx StatusBadge.

    Props:
        status — "active" | "disabled" (default "active")
    Slots:
        default override text (default auto-labels from status)
    ============================================================ !!}

@props(['status' => 'active'])

<?php $active = 'active' === $status; ?>

<span {{ $attributes->merge([
    'class' => match ($active) {
        true  => 'inline-flex items-center gap-1.5 rounded-full bg-[--green-tint] px-2.5 py-1 text-xs font-semibold text-[--green]',
        false => 'inline-flex items-center gap-1.5 rounded-full bg-[--bg-soft] px-2.5 py-1 text-xs font-semibold text-[--ink-3]',
    },
]) }}>
    <span class="size-1.5 rounded-full bg-current"></span>
    {{ $slot->default($active ? 'Active' : 'Disabled') }}
</span>
