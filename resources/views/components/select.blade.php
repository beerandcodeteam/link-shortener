{{-- ============================================================
    Select
    Styled native <select> with label, hint & error state.
    Uses Tailwind classes mapped from design.tokens in @theme.
   ============================================================ --}}

@props([
    'label'  => null,            // optional visible-label text
    'hint'   => null,            // optional hint/help text (no validation)
    'error'  => null,            // error message string or true (falls back to $errors)
])

@php
    $isValid = blank($error);

    // When error is true and no explicit message was given, try $errors bag
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
        <label for="{{ $attributes->get('id') }}" class="block text-xs font-medium text-ink-2 mb-1.5 select-none">{{ $label }}</label>
    @endif

    {{-- Wrapper: styled border + padding ring around the native select --}}
    <div {{
        $attributes->except(['type'])->merge([
            'wire:key'   => $attributes->get('wire:key', ''),
            'data-fieldset' => 'select',
        ])->class([
            // Base wrapper styling
            'relative flex items-stretch w-full rounded-md bg-white overflow-hidden',

            // Valid / invalid border and focus ring (applied on :focus-within)
            $isValid
                ? 'border border-line transition-border duration-150 focus-within:border-blue focus:ring-4 focus:ring-blue-tint/50'
                : 'border border-red transition-border duration-150 focus-within:border-red focus:ring-4 focus:ring-red-tint/50',

            // Padding consistent with Input component (py-3.5 px-4)
            'pl-4 pr-10 py-3.5',
        ])
    }}>

        {{-- Native <select>: remove default appearance, add custom chevron icon --}}
        <select {{
            $attributes->merge([
                'id' => uniqid('select-'),
            ])->class([
                // Remove native dropdown arrow (we render our own chevron)
                'appearance-none',

                // Core styles — inherit text/bg from wrapper
                'bg-transparent border-none outline-none flex-1 min-w-0 cursor-pointer py-0 px-0 pr-0 text-ink',

                // Allow option text to show through on hover (for the overlay approach)
                '[&>option]:text-ink [&>option]:bg-white [&>option]:text-sm',
            ])
        }}>
            {{ $slot }}
        </select>

        {{-- Custom chevron icon — mirrors iOS-like native picker --}}
        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
            <svg class="size-5 text-ink-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor">
                <path fill-rule="evenodd" d="M4.22 6.22a.75.75 0 0 1 1.06 0L8 8.94l2.72-2.72a.75.75 0 1 1 1.06 1.06l-3.25 3.25a.75.75 0 0 1-1.06 0L4.22 7.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
            </svg>
        </div>

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
