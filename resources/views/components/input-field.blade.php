<div class="relative">
    <label class="block text-xs font-medium mb-1 {{ $error ? 'text-red' : 'text-ink-2' }}">
        {{ $label }}
    </label>
    <input
        type="{{ $type ?? 'text' }}"
        {{ $attributes }}
    >
    @if($error)
        <span class="text-[12.5px] text-red mt-1">{{ $error }}</span>
    @endif
</div>
