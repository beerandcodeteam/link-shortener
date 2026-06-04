@props([
    'label' => null,
    'name' => null,
    'id' => null,
    'checked' => false,
    'value' => '1',
])

@php
    $id = $id ?? ($name ? 'check-'.$name : 'check-'.uniqid());
@endphp

<label for="{{ $id }}" class="inline-flex items-center gap-2 cursor-pointer select-none text-[14.5px] text-[var(--color-ink)]">
    <input
        type="checkbox"
        id="{{ $id }}"
        @if($name) name="{{ $name }}" @endif
        value="{{ $value }}"
        @checked($checked)
        {{ $attributes->merge(['class' => 'w-[18px] h-[18px] rounded border border-[var(--color-line)] accent-[var(--color-blue)] focus:outline-none focus:ring-2 focus:ring-[var(--color-blue-tint)]']) }}
    >
    @if($label) <span>{{ $label }}</span> @endif
</label>
