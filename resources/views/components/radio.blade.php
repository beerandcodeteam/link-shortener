@props([
    'label' => null,
    'error' => null,
    'name',
    'options' => [],
    'selected' => null,
])

@php
    $hasError = filled($error);
@endphp

<div class="field">
    @if ($label)
        <span class="field-label">{{ $label }}</span>
    @endif

    <div class="radio-group">
        @if (count($options))
            @foreach ($options as $value => $text)
                @php($optionId = $name . '-' . $loop->index)
                <label class="choice" for="{{ $optionId }}">
                    <input
                        type="radio"
                        id="{{ $optionId }}"
                        name="{{ $name }}"
                        value="{{ $value }}"
                        @checked((string) $selected === (string) $value)
                        class="choice-input is-radio {{ $hasError ? 'input-err' : '' }}"
                    >
                    <span>{{ $text }}</span>
                </label>
            @endforeach
        @else
            {{ $slot }}
        @endif
    </div>

    @if ($hasError)
        <span class="err-text">{{ $error }}</span>
    @endif
</div>
