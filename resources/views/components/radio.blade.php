@props([
    'label' => '',
    'error' => null,
])

<div class="flex items-center gap-3">
    <input
        type="radio"
        {{ $attributes }}
        class="h-4 w-4 text-blue focus:ring-blue ring-offset-1"
    >
    <label class="text-sm font-medium">{{ $label }}</label>
</div>
