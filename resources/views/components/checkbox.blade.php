@props([
    'label' => null,
    'error' => null,
    'name' => null,
    'id' => null,
    'value' => '1',
    'checked' => false,
])

@php
    $id ??= $name ?? 'checkbox-' . \Illuminate\Support\Str::random(6);
    $hasError = filled($error);
@endphp

<div class="field">
    <label class="choice" for="{{ $id }}">
        <input
            type="checkbox"
            id="{{ $id }}"
            @if ($name) name="{{ $name }}" @endif
            value="{{ $value }}"
            @checked($checked)
            {{ $attributes->merge(['class' => 'choice-input is-checkbox' . ($hasError ? ' input-err' : '')]) }}
        >
        @if ($label)
            <span>{{ $label }}</span>
        @endif
        {{ $slot }}
    </label>

    @if ($hasError)
        <span class="err-text">{{ $error }}</span>
    @endif
</div>
