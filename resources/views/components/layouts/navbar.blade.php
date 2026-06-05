<nav class="border-b border-line-soft bg-white/80 backdrop-blur-[20px] sticky top-0 z-50 h-12 px-6 md:px-8 flex items-center justify-between">
    <div class="flex items-center gap-3">
        <span class="font-semibold text-lg tracking-tight uppercase">Snip</span>
    </div>

    <div class="flex items-center gap-6 ml-2">
        @auth
            <a href="{{ route('dashboard') }}" class="hover:opacity-100 opacity-80 transition font-medium text-sm">Dashboard</a>
            <a href="/analytics" class="hover:opacity-100 opacity-80 transition font-medium text-sm">Analytics</a>
            <a href="/new" class="bg-blue px-4 py-1.5 rounded-full text-white hover:bg-blue-press transition duration-200 text-sm shadow-none">New link</a>
        @else
            <a href="{{ route('login') }}" class="hover:opacity-100 opacity-80 transition font-medium text-sm">Log in</a>
            <a href="{{ route('register') }}" class="bg-blue px-4 py-1.5 rounded-full text-white hover:bg-blue-press transition duration-200 text-sm shadow-none">Sign up</a>
        @endauth
    </div>
</nav>
