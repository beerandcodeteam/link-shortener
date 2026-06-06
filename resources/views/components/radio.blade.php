{{-- ============================================================
    Radio Group
    Horizontal pill-style radio group label selector with error state.
    Uses Tailwind classes mapped from design.tokens in @theme.

    Usage:
        <x-radio-group wire:model="frequency" :options="$options" />

    Where $options is an array like:
        ['daily' => 'Daily', 'weekly' => 'Weekly', 'monthly' => 'Monthly']
   ============================================================ --}}

@props([
    'label'  => null,            // optional visible-label text
    'hint'   => null,            // optional hint/help text (no validation)
    'error'  => null,            // error message string or true (falls back to $errors)
    'options',                   // required ['value' => 'Label'] associative array
    'align' => 'start',          // start | center | end (pill container alignment)
])

@php
    $isValid = blank($error);

    if ($error === true) {
        $error = filled($attributes->get('name'))
            ? ($errors->first((string) $attributes->get('name')) ?: null)
            : null;
        $isValid = blank($error);
    }
@endphp

{{-- Root wrapper mirrors .field from design-handoff --}}
<div class="w-full">

    {{-- Label mirrors .field-label --}}
    @if ($label)
        <label class="block text-xs font-medium text-ink-2 mb-1.5 select-none">{{ $label }}</label>
    @endif

    {{-- Pill group container — mirrors .segmented-control aesthetic --}}
    <div {{
        $attributes->except('class')->merge([
            'data-fieldset' => 'radio-group',
            'role'          => 'radiogroup',
        ])->class([
            // Flex wrapping: stack on extra-small, row otherwise
            'inline-flex items-stretch gap-1 w-full rounded-md border p-0.5',

            // Valid / invalid border state
            $isValid
                ? 'border-line bg-scaffold transition-border duration-150'
                : 'border-red bg-white transition-border duration-150',

            // Alignment of child pills
            match ($align) {
                'center' => 'justify-center',
                'end'    => 'justify-end',
                default  => 'justify-start',
            },
        ])
    }}>

        @foreach ($options as $value => $labelItem)
            <input type="hidden" name="{{ $attributes->get('name') }}" value="" wire:ignore />

            <label class="relative">
                {{-- Hidden native radio: required for accessibility & form submission --}}
                <input
                    type="radio"
                    name="{{ $attributes->get('name') }}"
                    value="{{ $value }}"
                    {{ $attributes->has('wire:model') ? $attributes->only('wire:model') : '' }}
                    class="peer sr-only" />

                {{-- Visual pill — mirrors .button / segmented-control styling --}}
                <span class="inline-flex items-center px-4 py-1.5 text-sm font-medium rounded-md cursor-pointer select-none transition-all duration-150 text-ink-3 bg-transparent peer-checked:bg-white peer-checked:text-ink peer-checked:shadow-sm peer-focus-visible:outline peer-focus-visible:outline-2 peer-focus-visible:outline-blue">{{ $labelItem }}</span>

            </label>
        @endforeach

    </div>

    {{-- Hint / helper ---}}
    @if ($hint)
        <span class="block mt-1 text-xs text-ink-3 select-none">{{ $hint }}</span>
    @endif

    {{-- Error message (mirrors .err-text) --}}
    @if ($error)
        <span class="block mt-1 text-sm text-red select-none">{{ is_string($error) ? $error : $errors->first($attributes->get("name")) }}</span>
    @endif

</div>
