@props([
    'label' => '',
    'error' => null,
])

<div class="relative">
    <label class="block text-xs font-medium mb-1 {{ $error ? 'text-red' : 'text-ink-2' }}">
        {{ $label }}
    </label>
    <div class="space-y-2">
        @foreach($options as $value => $label)
            <div class="flex items-center gap-3">
                <input
                    type="radio"
                    name="{{ $name }}"
                    value="{{ $value }}"
                    {{ $selected === $value ? 'checked' : '' }}
                    class="h-4 w-4 text-blue focus:ring-blue ring-offset-1"
                >
                <label class="text-sm font-medium">{{ $label }}</label>
            </div>
        @endforeach
    </div>
    @if($error)
        <span class="text-[12.5px] text-red mt-1">{{ $error }}</span>
    @endif
</div>
