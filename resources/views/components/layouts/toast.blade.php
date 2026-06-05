<div class="fixed bottom-8 left-1/2 -translate-x-1/2 z-[200]">
    @if(session()->has('flash'))
        <div class="flex items-center gap-3 bg-zinc-900 text-white px-6 py-3 rounded-full shadow-pop animate-fade-in">
            {{ session('flash') }}
        </div>
    @endif
</div>
