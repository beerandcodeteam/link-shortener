@props([
    'label' => null,
    'error' => null,
    'name' => null,
    'id' => null,
    'required' => false,
])

@php
    $id = $id ?? ($name ? 'field-'.$name : 'field-'.uniqid());
    $hasError = (bool) $error;
    $base = 'w-full border rounded-[var(--radius-md)] px-[15px] py-[13px] text-[16px] text-[var(--color-ink)] bg-white transition-[border-color,box-shadow] duration-150 focus:outline-none appearance-none';
    $state = $hasError
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

    <div class="relative">
        <select
            id="{{ $id }}"
            @if($name) name="{{ $name }}" @endif
            @if($required) required @endif
            {{ $attributes->merge(['class' => trim($base.' '.$state).' pr-10 bg-no-repeat'])}}
            style="background-image: url(&quot;data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2386868b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><polyline points='6 9 12 15 18 9'/></svg>&quot;); background-position: right 12px center;"
        >
            {{ $slot }}
        </select>
    </div>

    @if($error)
        <p class="text-[12.5px] text-[var(--color-red)]">{{ $error }}</p>
    @endif
</div>
