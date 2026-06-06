{{-- ============================================================
    Avatar
    Shows a circular initials avatar with a dropdown (Alpine).
    Mirrors ui.jsx Avatar.

    Props:
        name  — full name for initials extraction
        email — email address (shown in dropdown)
        logout — action to dispatch on sign-out (e.g. wire:click="logout")
    Slots:
        dropdown — custom dropdown content instead of defaults

    Example:
        <x-avatar :name="$user->name" :email="$user->email" logout="logout" />
    ============================================================ !!}

@props(['name' => '', 'email' => '', 'logout' => null])

@php
    $initials = strtoupper(
        collect(explode(' ', (string) $name))
            ->take(2)
            ->map(fn($word) => $word[0] ?? '')
            ->join('')
    );
    if ('' === $initials) { $initials = '??'; }
@endphp

<div x-data="{ open: false }" @click.away="open = false" class="relative">
    <button
        type="button"
        @click="open = !open"
        class="flex size-8 items-center justify-center rounded-full border-0 bg-[--ink] text-xs font-semibold text-white cursor-pointer"
    >{{ $initials }}</button>

    <template x-teleport="body">
        <div
            x-show="open"
            @keydown.escape.window="open = false"
            class="absolute right-0 top-[40px] z-[100] w-[220px] rounded-xl border border-[--line-soft] bg-white p-2 text-left shadow-pop"
            style="animation: modal-in .18s ease"
        >
            <div class="px-2.5 pb-2.5">
                <div class="text-sm font-semibold">{{ $name }}</div>
                <div class="text-[12.5px] text-[--ink-3]">{{ $email }}</div>
            </div>
            <hr class="divider" />
            @if (null !== $logout)
                <button
                    type="button"
                    wire:click="{{ $logout }}"
                    @mouseenter="$el.style.background='var(--bg-soft)'"
                    @mouseleave="$el.style.background='none'"
                    class="w-full cursor-pointer rounded-lg border-0 bg-none px-2.5 py-2.25 text-left text-[14px] leading-tight text-[--ink] transition-colors hover:bg-[--bg-soft]"
                >Sign out</button>
            @endif
            {{ $slot }}
        </div>
    </template>
</div>
