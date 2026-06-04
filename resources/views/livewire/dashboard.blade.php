<div class="flex flex-col gap-7">
    <header class="flex flex-col gap-2">
        <span class="eyebrow">Dashboard</span>
        <h1 class="h1">Your short links</h1>
        <p class="body text-[var(--color-ink-3)]">
            @auth
                Welcome back, {{ auth()->user()->name }}.
            @endauth
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
    @endif

    {{-- Create form (US-4.2) --}}
    <section
        class="rounded-[var(--radius-lg)] border border-[var(--color-line)] bg-white p-6"
        style="box-shadow: var(--shadow-card);"
        data-testid="dashboard-create-form"
    >
        <form wire:submit="createLink" novalidate>
            <div class="flex flex-col gap-[7px]">
                <div class="flex items-stretch border rounded-[var(--radius-md)] overflow-hidden bg-white transition-[border-color,box-shadow] focus-within:border-[var(--color-blue)] focus-within:shadow-[0_0_0_4px_var(--color-blue-tint)] {{ $errors->has('original_url') ? 'border-[var(--color-red)] focus-within:shadow-[0_0_0_4px_var(--color-red-tint)]' : 'border-[var(--color-line)]' }}">
                    <span class="flex items-center px-1 pl-[15px] text-[var(--color-ink-3)] text-[17px]">
                        <x-ui.icon name="link" :size="19" />
                    </span>
                    <input
                        type="url"
                        wire:model="original_url"
                        placeholder="Paste a long link, e.g. https://example.com/very/long/path"
                        class="w-full border-0 px-[15px] py-[13px] text-[17px] text-[var(--color-ink)] placeholder:text-[var(--color-ink-3)] focus:outline-none focus:ring-0"
                        data-testid="dashboard-original-url"
                    >
                </div>
                @if ($errors->has('original_url'))
                    <p class="text-[12.5px] text-[var(--color-red)]" data-testid="error-original-url">{{ $errors->first('original_url') }}</p>
                @endif
            </div>

            @if ($showCustom)
                <div class="flex flex-col gap-[7px] mt-3">
                    <div class="flex items-stretch border rounded-[var(--radius-md)] overflow-hidden bg-white transition-[border-color,box-shadow] focus-within:border-[var(--color-blue)] focus-within:shadow-[0_0_0_4px_var(--color-blue-tint)] {{ $errors->has('custom_code') ? 'border-[var(--color-red)] focus-within:shadow-[0_0_0_4px_var(--color-red-tint)]' : 'border-[var(--color-line)]' }}">
                        <span class="flex items-center px-1 pl-[15px] text-[15px] text-[var(--color-ink-2)] whitespace-nowrap">
                            {{ request()->getHost() }}/
                        </span>
                        <input
                            type="text"
                            wire:model="custom_code"
                            placeholder="custom-code (optional)"
                            class="w-full border-0 px-[15px] py-[13px] text-[16px] text-[var(--color-ink)] placeholder:text-[var(--color-ink-3)] focus:outline-none focus:ring-0"
                            data-testid="dashboard-custom-code"
                        >
                    </div>
                    @if ($errors->has('custom_code'))
                        <p class="text-[12.5px] text-[var(--color-red)]" data-testid="error-custom-code">{{ $errors->first('custom_code') }}</p>
                    @endif
                </div>
            @endif

            <div class="flex items-center justify-between mt-4 gap-3">
                <button
                    type="button"
                    wire:click="toggleCustom"
                    class="inline-flex items-center gap-1.5 text-[14px] text-[var(--color-blue)] hover:underline bg-transparent border-0 cursor-pointer"
                >
                    @if ($showCustom)
                        <x-ui.icon name="close" :size="15" />
                    @else
                        <x-ui.icon name="plus" :size="15" />
                    @endif
                    {{ $showCustom ? 'Remove custom code' : 'Customize the code' }}
                </button>

                <x-ui.button type="submit" variant="primary" size="lg" icon="link">
                    Shorten link
                </x-ui.button>
            </div>
        </form>
    </section>

    {{-- Link list (US-4.1) --}}
    <section
        class="rounded-[var(--radius-lg)] border border-[var(--color-line)] bg-white"
        style="box-shadow: var(--shadow-card);"
        data-testid="dashboard-link-list"
    >
        <div class="px-6 pt-6 pb-3 flex items-center justify-between">
            <h2 class="h3">Your links</h2>
            <span class="small text-[var(--color-ink-3)]">
                {{ $links->total() }} {{ \Illuminate\Support\Str::plural('link', $links->total()) }}
            </span>
        </div>

        @if ($links->count() === 0)
            <div
                class="px-6 pb-8 pt-4 text-center"
                data-testid="dashboard-empty-state"
            >
                <p class="body text-[var(--color-ink-3)]">No links yet. Use the form above to create your first short link.</p>
            </div>
        @else
            <ul class="divide-y divide-[var(--color-line-soft)]" data-testid="dashboard-link-rows">
                @foreach ($links as $link)
                    @php
                        $shortUrl = url('/'.$link->short_code);
                        $status = $link->linkStatus?->slug === 'active' ? 'active' : 'disabled';
                    @endphp
                    <li
                        class="px-6 py-4 flex items-center gap-4"
                        data-testid="link-row"
                        data-link-id="{{ $link->id }}"
                    >
                        <div class="flex-1 min-w-0 flex flex-col gap-1">
                            <a
                                href="{{ route('links.show', $link) }}"
                                class="mono text-[15px] font-semibold text-[var(--color-blue)] truncate"
                                data-testid="link-short-url"
                            >{{ $shortUrl }}</a>
                            <span class="small text-[var(--color-ink-3)] truncate" data-testid="link-original-url">
                                → {{ $link->original_url }}
                            </span>
                        </div>
                        <div class="hidden sm:flex items-center gap-4 shrink-0">
                            <span class="small text-[var(--color-ink-2)]" data-testid="link-click-count">
                                {{ number_format($link->click_count) }} {{ \Illuminate\Support\Str::plural('click', $link->click_count) }}
                            </span>
                            <span class="small text-[var(--color-ink-3)]" data-testid="link-created-at">
                                {{ $link->created_at->format('M j, Y') }}
                            </span>
                            <x-ui.status-badge :status="$status" />
                        </div>
                        <div class="flex items-center gap-1 shrink-0">
                            <x-ui.copy-button :text="$shortUrl" />
                            <a
                                href="{{ route('links.show', $link) }}"
                                class="inline-grid place-items-center w-9 h-9 rounded-full bg-transparent text-[var(--color-ink-2)] hover:bg-[var(--color-bg-soft)] hover:text-[var(--color-ink)]"
                                title="View details"
                                data-testid="link-detail-link"
                            >
                                <x-ui.icon name="chart" :size="18" />
                            </a>
                            <form
                                method="POST"
                                action="{{ route('links.toggle', $link) }}"
                                class="inline-flex"
                                data-testid="link-toggle-form"
                            >
                                @csrf
                                <button
                                    type="submit"
                                    title="{{ $status === 'active' ? 'Disable' : 'Enable' }}"
                                    data-testid="link-toggle-button"
                                    class="inline-grid place-items-center w-9 h-9 rounded-full bg-transparent text-[var(--color-ink-2)] hover:bg-[var(--color-bg-soft)] hover:text-[var(--color-ink)] border-0 cursor-pointer"
                                >
                                    @if ($status === 'active')
                                        <span class="inline-grid place-items-center w-4 h-4 rounded-full border-2 border-current"></span>
                                    @else
                                        <span class="inline-grid place-items-center w-4 h-4 rounded-full bg-current"></span>
                                    @endif
                                </button>
                            </form>
                            <button
                                type="button"
                                wire:click="confirmDelete({{ $link->id }})"
                                title="Delete"
                                data-testid="link-delete-button"
                                class="inline-grid place-items-center w-9 h-9 rounded-full bg-transparent text-[var(--color-red)] hover:bg-[var(--color-red-tint)] border-0 cursor-pointer"
                            >
                                <x-ui.icon name="trash" :size="18" />
                            </button>
                        </div>
                    </li>
                @endforeach
            </ul>

            @if ($links->hasPages())
                <div class="px-6 py-4 border-t border-[var(--color-line-soft)]" data-testid="dashboard-pagination">
                    {{ $links->onEachSide(1)->links() }}
                </div>
            @endif
        @endif
    </section>

    @if ($confirmingDelete !== null)
        @php
            $pendingLink = $links->getCollection()->firstWhere('id', $confirmingDelete)
                ?? \App\Models\Link::query()
                    ->where('user_id', auth()->id())
                    ->whereKey($confirmingDelete)
                    ->first();
        @endphp
        <div
            class="fixed inset-0 z-[150] bg-black/32 grid place-items-center p-6 backdrop-blur-[2px]"
            style="animation: fade .2s ease;"
            data-testid="delete-confirm-modal"
            @keydown.escape.window="$wire.cancelDelete()"
        >
            <div
                class="bg-white rounded-[var(--radius-lg)] w-full shadow-[var(--shadow-pop)] p-7"
                style="max-width: 460px; animation: modal-in .26s cubic-bezier(.2,.8,.2,1);"
            >
                <h2 class="h3 mb-2">Delete this short link?</h2>
                <p class="body text-[var(--color-ink-2)] mb-4">
                    This will permanently remove the link and stop the short code from redirecting.
                </p>
                @if ($pendingLink)
                    <div class="rounded-[var(--radius-md)] bg-[var(--color-bg-soft)] p-3 mb-5 flex flex-col gap-1" data-testid="delete-confirm-target">
                        <span class="mono text-[14.5px] font-semibold text-[var(--color-blue)] break-all">
                            {{ url('/'.$pendingLink->short_code) }}
                        </span>
                        <span class="small text-[var(--color-ink-3)] break-all">
                            → {{ $pendingLink->original_url }}
                        </span>
                    </div>
                @endif
                <div class="flex items-center justify-end gap-2">
                    <button
                        type="button"
                        wire:click="cancelDelete"
                        data-testid="delete-confirm-cancel"
                        class="btn btn-soft btn-sm inline-flex items-center justify-center border-0 rounded-[var(--radius-pill)] text-[13.5px] px-[14px] py-2 font-medium"
                        style="background: var(--color-bg-soft); color: var(--color-ink);"
                    >
                        Cancel
                    </button>
                    <button
                        type="button"
                        wire:click="deleteConfirmed"
                        data-testid="delete-confirm-confirm"
                        class="btn btn-sm inline-flex items-center justify-center border-0 rounded-[var(--radius-pill)] text-[13.5px] px-[14px] py-2 font-medium"
                        style="background: var(--color-red); color: #fff;"
                    >
                        Delete
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
