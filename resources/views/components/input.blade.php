{{-- ============================================================
    Input
    Text / url / email / password field with label, hint & error state.
    Supports leading + trailing named slots (e.g. icon prefix).
    Uses Tailwind classes mapped from design.tokens in @theme.
   ============================================================ --}}

@props([
    'type'   => 'text',          // text | url | email | password
    'label'  => null,            // optional visible-label text
    'hint'   => null,            // optional hint/help text (no validation)
    'error'  => null,            // error message string or true (falls back to $errors)

    /* Named slots: leading = before the input | trailing = after */
])

@php
    $isValid   = blank($error);
    $hasSlots  = isset($leading) || isset($trailing);

    // When error is true and no explicit message was given, try $errors bag
    if ($error === true) {
        $error = filled($attributes->get('name'))
            ? ($errors->first((string) $attributes->get('name')) ?: null)
            : null;
        $isValid = blank($error);
    }
@endphp

{{-- Root wrapper mirrors .field --}}
<div class="w-full">

    {{-- Label mirrors .field-label from design-handoff --}}
    @if ($label)
        <label for="{{ $attributes->get('id', 'input-'.uniqid()) }}" class="block text-xs font-medium text-ink-2 mb-1.5 select-none">{{ $label }}</label>
    @endif

    {{-- Wrapper: input-group when slot content, plain wrapper otherwise --}}
    @if ($hasSlots)

        {{-- input-group — mirrors .input-group in design-handoff + :focus-within --}}
        <div class="flex items-stretch {{ $isValid ? 'border border-line rounded-md bg-white focus-within:border-blue focus:ring-4 focus:ring-blue-tint/50' : 'border border-red rounded-md bg-white focus:border-red focus:ring-4 focus:ring-red-tint/50' }} transition-border duration-150">

            {{-- Leading before input --}}
            @if (isset($leading))
                <span class="flex items-center px-4 pr-0 text-ink-3 bg-white whitespace-nowrap text-sm">{{ $leading }}</span>
            @endif

            {{-- Native input --}}
            <input {{
                $attributes->except('class')->merge([
                    'type' => $type,
                    'placeholder' => '',
                ])->class([
                    // Core sizing & appearance (mirrors design-handoff .input)
                    'appearance-none bg-transparent border-none outline-none flex-1 min-w-0 py-3.5 text-ink placeholder:ink-3',

                    // Padding adjustment when leading exists
                    isset($leading) ? 'pl-0' : 'px-4',
                ])
            }} />

            {{-- Trailing after input --}}
            @if (isset($trailing))
                <span class="flex items-center pl-0 pr-4 text-ink-3">{{ $trailing }}</span>
            @endif

        </div>

    @else

        {{-- Standalone input — mirrors .input + .input-err --}}
        <input {{
            $attributes->merge([
                'type' => $type,
                'placeholder' => '',
            ])->class([
                // Core sizing & appearance (mirrors design-handoff .input)
                'block w-full rounded-md border py-3.5 px-4 text-ink placeholder:ink-3 bg-white',

                // Transition only what we animate
                'border-line transition-border duration-150 focus:border-blue focus:ring-4 focus:ring-blue-tint/50',

                // Error state (mirrors .input-err + .input-err:focus)
                ! $isValid ? '!border-red focus:!ring-red-tint' : '',

                'rounded-md',
            ])
        }} />

    @endif

    {{-- Hint / helper ---}}
    @if ($hint)
        <span class="block mt-1 text-xs text-ink-3 select-none">{{ $hint }}</span>
    @endif

    {{-- Error message (mirrors .err-text) --}}
    @if ($error)
        <span class="block mt-1 text-sm text-red select-none">{{ is_string($error) ? $error : $errors->first($attributes->get("name")) }}</span>
    @endif

</div>
