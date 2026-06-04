@props([
    'status' => 'active', // active | disabled
])

@if($status === 'active')
    <span class="inline-flex items-center gap-[5px] text-[12px] font-semibold px-[10px] py-1 rounded-[var(--radius-pill)] bg-[var(--color-green-tint)] text-[var(--color-green)]">
        <span class="w-[6px] h-[6px] rounded-full bg-current"></span>
        Active
    </span>
@else
    <span class="inline-flex items-center gap-[5px] text-[12px] font-semibold px-[10px] py-1 rounded-[var(--radius-pill)] bg-[var(--color-bg-soft)] text-[var(--color-ink-3)]">
        <span class="w-[6px] h-[6px] rounded-full bg-current"></span>
        Disabled
    </span>
@endif
