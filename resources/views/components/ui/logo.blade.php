@props(['size' => 26])

@php $s = (int) $size; @endphp

<span class="inline-flex items-center gap-2 font-semibold tracking-[-0.03em] text-[var(--color-ink)]" {{ $attributes }}>
    <span class="inline-grid place-items-center" style="width: {{ $s }}px; height: {{ $s }}px;">
        <svg width="{{ $s }}" height="{{ $s }}" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <rect x="2" y="6" width="22" height="14" rx="7" fill="#0071e3"/>
            <rect x="14" y="12" width="16" height="14" rx="7" fill="#1d1d1f" stroke="#fff" stroke-width="2"/>
        </svg>
    </span>
    <span class="text-[19px]">Snip</span>
</span>
