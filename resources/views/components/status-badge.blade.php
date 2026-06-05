<div {{ $attributes->merge(['class' => 'inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold ' . ($status === 'active' ? 'bg-green-tint text-green border border-green-hover' : 'bg-gray-100 text-gray-500')])}}>
    <span class="w-1.5 h-1.5 rounded-full {{ $status === 'active' ? 'bg-green' : 'bg-gray-400' }}"></span>
    {{ ucfirst($status) }}
</div>
