@props([
    'type' => 'text',
    'label' => null,
    'hint' => null,
    'error' => null,
    'name' => null,
    'id' => null,
    'placeholder' => null,
    'value' => null,
    'required' => false,
    'autofocus' => false,
    'autocomplete' => null,
])

@php
    $id = $id ?? ($name ? 'field-'.$name : 'field-'.uniqid());
    $hasError = (bool) $error;
    $inputBase = 'w-full border rounded-[var(--radius-md)] px-[15px] py-[13px] text-[16px] text-[var(--color-ink)] bg-white transition-[border-color,box-shadow] duration-150 placeholder:text-[var(--color-ink-3)] focus:outline-none';
    $inputState = $hasError
        ? 'border-[var(--color-red)] focus:shadow-[0_0_0_4px_var(--color-red-tint)]'
        : 'border-[var(--color-line)] focus:border-[var(--color-blue)] focus:shadow-[0_0_0_4px_var(--color-blue-tint)]';
@endphp

<div class="flex flex-col gap-[7px]">
    @if($label)
        <label for="{{ $id }}" class="text-[13px] font-medium text-[var(--color-ink-2)]">
            {{ $label }}
            @if($required) <span class="text-[var(--color-red)]">*</span> @endif
        </label>
    @endif

    @isset($leading)
        <div class="flex items-stretch border border-[var(--color-line)] rounded-[var(--radius-md)] overflow-hidden bg-white transition-[border-color,box-shadow] focus-within:border-[var(--color-blue)] focus-within:shadow-[0_0_0_4px_var(--color-blue-tint)]">
            <span class="flex items-center px-1 pl-[15px] text-[var(--color-ink-3)] text-[16px] whitespace-nowrap">
                {{ $leading }}
            </span>
            <input
                type="{{ $type }}"
                id="{{ $id }}"
                @if($name) name="{{ $name }}" @endif
                @if($placeholder) placeholder="{{ $placeholder }}" @endif
                @if($value !== null) value="{{ $value }}" @endif
                @if($autocomplete) autocomplete="{{ $autocomplete }}" @endif
                @if($required) required @endif
                @if($autofocus) autofocus @endif
                {{ $attributes->merge(['class' => $inputBase.' border-0 shadow-none focus:shadow-none bg-transparent']) }}
            >
        </div>
    @else
        <input
            type="{{ $type }}"
            id="{{ $id }}"
            @if($name) name="{{ $name }}" @endif
            @if($placeholder) placeholder="{{ $placeholder }}" @endif
            @if($value !== null) value="{{ $value }}" @endif
            @if($autocomplete) autocomplete="{{ $autocomplete }}" @endif
            @if($required) required @endif
            @if($autofocus) autofocus @endif
            {{ $attributes->merge(['class' => trim($inputBase.' '.$inputState)]) }}
        >
    @endisset

    @if($hint && !$error)
        <p class="text-[12.5px] text-[var(--color-ink-3)]">{{ $hint }}</p>
    @endif
    @if($error)
        <p class="text-[12.5px] text-[var(--color-red)]">{{ $error }}</p>
    @endif
</div>
