@props([
    'type' => 'text',
    'label' => null,
    'hint' => null,
    'error' => null,
    'name' => null,
    'id' => null,
])

@php
    $id ??= $name ?? 'input-' . \Illuminate\Support\Str::random(6);
    $hasError = filled($error);
    $hasGroup = isset($leading) || isset($trailing);
@endphp

<div class="field">
    @if ($label)
        <label class="field-label" for="{{ $id }}">{{ $label }}</label>
    @endif

    @if ($hasGroup)
        <div class="input-group {{ $hasError ? 'input-err' : '' }}">
            @isset($leading)
                <span class="input-prefix">{{ $leading }}</span>
            @endisset

            <input
                type="{{ $type }}"
                id="{{ $id }}"
                @if ($name) name="{{ $name }}" @endif
                {{ $attributes->merge(['class' => 'input']) }}
            >

            @isset($trailing)
                <span class="input-suffix">{{ $trailing }}</span>
            @endisset
        </div>
    @else
        <input
            type="{{ $type }}"
            id="{{ $id }}"
            @if ($name) name="{{ $name }}" @endif
            {{ $attributes->merge(['class' => 'input' . ($hasError ? ' input-err' : '')]) }}
        >
    @endif

    @if ($hasError)
        <span class="err-text">{{ $error }}</span>
    @elseif ($hint)
        <span class="hint-text">{{ $hint }}</span>
    @endif
</div>
