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
