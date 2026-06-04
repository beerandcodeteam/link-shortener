<div class="flex flex-col gap-7">
    <header class="flex flex-col gap-2">
        <span class="eyebrow">Dashboard</span>
        <h1 class="h1">Your short links</h1>
        <p class="body text-[var(--color-ink-3)]">
            @if ($justCreated)
                Nice — your new short link is ready below. Copy it and start sharing.
            @else
            @auth
                Welcome back, {{ auth()->user()->name }}.
            @else
                Sign in to manage your links.
            @endauth
            @endif
        </p>
    </header>

    @if ($justCreated)
        @php
            $shortUrl = url('/'.$justCreated->short_code);
        @endphp
        <section
            class="rounded-[var(--radius-lg)] border border-[var(--color-line)] bg-white p-6"
            style="box-shadow: var(--shadow-card);"
            data-testid="just-created-card"
        >
            <span class="eyebrow text-[var(--color-green)]">Link ready</span>
            <div class="mt-2 flex flex-col gap-1">
                <a
                    href="{{ $shortUrl }}"
                    class="mono text-[20px] font-semibold text-[var(--color-blue)] break-all"
                    data-testid="just-created-short-url"
                >{{ $shortUrl }}</a>
                <span class="small text-[var(--color-ink-3)] break-all" data-testid="just-created-original-url">
                    → {{ $justCreated->original_url }}
                </span>
            </div>
            <div class="mt-4 flex items-center gap-2">
                <x-ui.copy-button :text="$shortUrl" label="Copy link" />
                <x-ui.button variant="soft" size="sm" :href="route('home')">Create another</x-ui.button>
            </div>
        </section>
    @else
        <section
            class="rounded-[var(--radius-lg)] border border-dashed border-[var(--color-line)] bg-[var(--color-bg-soft)] p-10 text-center"
        >
            <p class="body text-[var(--color-ink-3)]">
                Your links will appear here as soon as you create one.
            </p>
            <div class="mt-4">
                <x-ui.button variant="primary" size="sm" :href="route('home')">
                    <x-slot:icon>plus</x-slot:icon>
                    Create your first short link
                </x-ui.button>
            </div>
        </section>
    @endif
</div>
