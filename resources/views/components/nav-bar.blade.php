@props([
    'user' => null,
])

@php
    $user ??= auth()->user();
    $onLinks = request()->is('dashboard*') || request()->is('links*');
@endphp

<nav {{ $attributes->merge(['class' => 'nav']) }}>
    <div class="nav-inner">
        <a href="{{ $user ? url('/dashboard') : url('/') }}" style="cursor: pointer">
            <x-logo />
        </a>

        @if (! $user)
            <div class="nav-links">
                <a class="nav-link" href="{{ url('/') }}">Shorten</a>
                <a class="nav-link" href="{{ url('/#features') }}">Features</a>
                <a class="nav-link" href="{{ url('/#pricing') }}">Pricing</a>
            </div>
        @else
            <div class="nav-links">
                <a class="nav-link {{ $onLinks ? 'nav-link-active' : '' }}" href="{{ url('/dashboard') }}">Links</a>
                <a class="nav-link" href="{{ url('/dashboard') }}">Analytics</a>
            </div>
        @endif

        <div class="nav-spacer"></div>

        @if (! $user)
            <div style="display: flex; align-items: center; gap: 8px">
                <x-button variant="ghost" size="sm" :href="url('/login')">Log in</x-button>
                <x-button variant="primary" size="sm" :href="url('/register')">Sign up</x-button>
            </div>
        @else
            <div style="display: flex; align-items: center; gap: 14px">
                <x-button variant="primary" size="sm" :href="url('/dashboard?create=1')" icon="plus">New link</x-button>
                <x-avatar :name="$user->name" :email="$user->email ?? null" />
            </div>
        @endif
    </div>
</nav>
