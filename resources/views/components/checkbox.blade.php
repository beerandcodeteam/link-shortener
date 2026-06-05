<div class="flex items-center gap-2">
    <input
        type="checkbox"
        {{ $attributes }}
        class="h-4 w-4 rounded border-gray-300 text-blue focus:ring-blue ring-offset-1"
    >
    <label class="text-sm font-medium {{ $error ? 'text-red' : 'text-ink-2' }}">
        {{ $label }}
    </label>
</div>
