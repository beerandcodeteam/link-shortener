@props([
    'name',
    'email' => null,
    'menu' => true,
    'logoutAction' => '/logout',
])

@php
    $initials = collect(preg_split('/\s+/', trim((string) $name)))
        ->filter()
        ->map(fn ($word) => mb_strtoupper(mb_substr($word, 0, 1)))
        ->take(2)
        ->implode('');
    $initials = $initials !== '' ? $initials : '?';
@endphp

@if ($menu)
    <div {{ $attributes }} x-data="{ open: false }" @mousedown.outside="open = false" style="position: relative">
        <button
            type="button"
            @click="open = !open"
            style="width: 32px; height: 32px; border-radius: 50%; border: none; cursor: pointer; background: var(--ink); color: #fff; font-size: 12.5px; font-weight: 600; letter-spacing: 0;"
        >{{ $initials }}</button>

        <div x-show="open" x-cloak class="menu">
            <div style="padding: 8px 10px 10px">
                <div style="font-size: 14px; font-weight: 600">{{ $name }}</div>
                @if ($email)
                    <div style="font-size: 12.5px; color: var(--ink-3)">{{ $email }}</div>
                @endif
            </div>
            <hr class="divider">
            <form method="POST" action="{{ $logoutAction }}">
                @csrf
                <button type="submit" class="menu-item">Sign out</button>
            </form>
        </div>
    </div>
@else
    <span
        {{ $attributes->merge(['style' => 'width: 32px; height: 32px; border-radius: 50%; background: var(--ink); color: #fff; font-size: 12.5px; font-weight: 600; display: inline-grid; place-items: center; letter-spacing: 0;']) }}
    >{{ $initials }}</span>
@endif
