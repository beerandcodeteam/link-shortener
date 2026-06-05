<div class="relative">
    <label class="block text-xs font-medium mb-1 {{ $error ? 'text-red' : 'text-ink-2' }}">
        {{ $label }}
    </label>
    <select
        class="w-full border border-line px-4 py-3 rounded-md bg-white focus:outline-none focus:border-blue focus:ring-4 focus:ring-blue-tint transition-all duration-150"
        {{ $attributes }}
    >
        @foreach($options as $value => $label)
            <option value="{{ $value }}" {{ $selectedValue == $value ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
    </select>
    @if($error)
        <span class="text-[12.5px] text-red mt-1">{{ $error }}</span>
    @endif
</div>
