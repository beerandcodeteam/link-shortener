@props([
    'label' => null,
    'hint' => null,
    'error' => null,
    'name' => null,
    'id' => null,
    'options' => [],
    'selected' => null,
    'placeholder' => null,
])

@php
    $id ??= $name ?? 'select-' . \Illuminate\Support\Str::random(6);
    $hasError = filled($error);
@endphp

<div class="field">
    @if ($label)
        <label class="field-label" for="{{ $id }}">{{ $label }}</label>
    @endif

    <select
        id="{{ $id }}"
        @if ($name) name="{{ $name }}" @endif
        {{ $attributes->merge(['class' => 'select' . ($hasError ? ' input-err' : '')]) }}
    >
        @if ($placeholder)
            <option value="" disabled @selected($selected === null || $selected === '')>{{ $placeholder }}</option>
        @endif

        @if (count($options))
            @foreach ($options as $value => $text)
                <option value="{{ $value }}" @selected((string) $selected === (string) $value)>{{ $text }}</option>
            @endforeach
        @else
            {{ $slot }}
        @endif
    </select>

    @if ($hasError)
        <span class="err-text">{{ $error }}</span>
    @elseif ($hint)
        <span class="hint-text">{{ $hint }}</span>
    @endif
</div>
