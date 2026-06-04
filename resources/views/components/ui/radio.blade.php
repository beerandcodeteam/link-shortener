@props([
    'name' => null,
    'options' => [],     // [['value' => 'a', 'label' => 'A'], ...]
    'label' => null,
    'error' => null,
    'value' => null,     // current checked value
])

@php
    $groupName = $name ?? 'radio-'.uniqid();
@endphp

<fieldset class="flex flex-col gap-2">
    @if($label)
        <legend class="text-[13px] font-medium text-[var(--color-ink-2)] mb-1">{{ $label }}</legend>
    @endif

    @foreach($options as $opt)
        <label class="inline-flex items-center gap-2 cursor-pointer text-[14.5px] text-[var(--color-ink)]">
            <input
                type="radio"
                name="{{ $groupName }}"
                value="{{ $opt['value'] }}"
                @checked(($value ?? null) === $opt['value'])
                {{ $attributes->merge(['class' => 'w-[18px] h-[18px] border border-[var(--color-line)] accent-[var(--color-blue)] focus:outline-none focus:ring-2 focus:ring-[var(--color-blue-tint)]']) }}
            >
            <span>{{ $opt['label'] }}</span>
        </label>
    @endforeach

    @if($error)
        <p class="text-[12.5px] text-[var(--color-red)]">{{ $error }}</p>
    @endif
</fieldset>
