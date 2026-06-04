@once
    @push('styles')
        <style>
            [x-cloak] { display: none !important; }
            @keyframes fade { from { opacity: 0; } to { opacity: 1; } }
            @keyframes modal-in { from { opacity: 0; transform: translateY(16px) scale(.98); } to { opacity: 1; transform: none; } }
            @keyframes toast-in { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
        </style>
    @endpush
@endonce

<div class="toast-wrap fixed bottom-7 left-1/2 -translate-x-1/2 z-[200] flex flex-col items-center gap-2" x-data x-init="
    window.addEventListener('toast', (e) => {
        $wire.push(e.detail?.msg ?? '', e.detail?.icon ?? 'check');
        setTimeout(() => $wire.dismiss($wire.toasts.at(-1)?.id ?? ''), 2400);
    });
">
    @foreach($toasts as $toast)
        <div
            wire:key="toast-{{ $toast['id'] }}"
            class="flex items-center gap-[10px] text-white px-[18px] py-3 rounded-[var(--radius-pill)] text-[14.5px] font-medium"
            style="background: rgba(29,29,31,.92); backdrop-filter: blur(10px); box-shadow: var(--shadow-pop); animation: toast-in .28s cubic-bezier(.2,.8,.2,1);"
        >
            <span class="text-[#4ade80]">
                <x-ui.icon :name="$toast['icon'] ?? 'check'" :size="18" />
            </span>
            <span>{{ $toast['msg'] }}</span>
        </div>
    @endforeach
</div>
