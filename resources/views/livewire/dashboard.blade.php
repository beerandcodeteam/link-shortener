<div class="wrap" style="width: 100%">
    <div style="display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 24px">
        <h1 class="h2">Your links</h1>
        <x-button variant="primary" size="sm" icon="plus" wire:click="$set('showCreate', true)">New link</x-button>
    </div>

    @if ($createdLink)
        <div wire:key="created-{{ $createdLink->id }}" class="card" style="padding: 20px; margin-bottom: 24px; border-color: var(--green); display: flex; align-items: center; justify-content: space-between; gap: 16px">
            <div style="min-width: 0">
                <p class="small" style="color: var(--green); font-weight: 600">Your short link is ready</p>
                <a class="mono" href="{{ route('redirect', $createdLink->short_code) }}" target="_blank">
                    {{ route('redirect', $createdLink->short_code) }}
                </a>
                <p class="small" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap">{{ $createdLink->original_url }}</p>
            </div>
            <x-copy-button :text="route('redirect', $createdLink->short_code)" label="Copy" />
        </div>
    @endif

    @forelse ($links as $link)
        <div wire:key="link-{{ $link->id }}" class="card" style="padding: 16px 20px; margin-bottom: 12px; display: flex; align-items: center; justify-content: space-between; gap: 16px">
            <div style="min-width: 0; flex: 1">
                <div style="display: flex; align-items: center; gap: 10px">
                    <a class="mono" href="{{ route('links.show', $link) }}" wire:navigate>{{ $link->short_code }}</a>
                    <x-status-badge :status="$link->isActive ? 'active' : 'disabled'" />
                </div>
                <p class="small" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap">{{ $link->original_url }}</p>
                <p class="small" style="color: var(--ink-3)">{{ $link->click_count }} clicks · {{ $link->created_at->format('M j, Y') }}</p>
            </div>
            <div style="display: flex; align-items: center; gap: 8px; flex-shrink: 0">
                <x-copy-button :text="route('redirect', $link->short_code)" />
                <a class="btn-icon" href="{{ route('links.show', $link) }}" title="View details" wire:navigate>
                    <x-icon name="eye" :size="18" />
                </a>
                <button type="button" class="btn-icon" title="{{ $link->isActive ? 'Disable' : 'Enable' }}" wire:click="toggleStatus({{ $link->id }})">
                    <x-icon name="power" :size="18" />
                </button>
                <button type="button" class="btn-icon" title="Delete" wire:click="confirmDelete({{ $link->id }})">
                    <x-icon name="trash" :size="18" />
                </button>
            </div>
        </div>
    @empty
        <div class="card" style="padding: 48px 24px; text-align: center">
            <p class="body" style="margin-bottom: 16px">No links yet.</p>
            <x-button variant="primary" size="sm" icon="plus" wire:click="$set('showCreate', true)">Create your first link</x-button>
        </div>
    @endforelse

    @if ($links->hasPages())
        <div style="margin-top: 24px">
            {{ $links->links() }}
        </div>
    @endif

    {{-- Create link modal --}}
    @if ($showCreate)
        <div class="scrim" wire:click.self="$set('showCreate', false)" x-on:keydown.escape.window="$wire.set('showCreate', false)">
            <div class="modal" style="max-width: 460px">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px">
                    <h2 class="h3">New short link</h2>
                    <button type="button" class="btn-icon" wire:click="$set('showCreate', false)" title="Close">
                        <x-icon name="x" :size="18" />
                    </button>
                </div>

                <form wire:submit="create" style="display: flex; flex-direction: column; gap: 16px">
                    <x-input
                        label="Long URL"
                        placeholder="https://example.com/very/long/link"
                        wire:model="form.original_url"
                        :error="$errors->first('form.original_url')"
                    />

                    <x-input
                        label="Custom short code (optional)"
                        placeholder="my-link"
                        wire:model="form.custom_code"
                        :error="$errors->first('form.custom_code')"
                    />

                    <x-button type="submit" variant="primary" wire:loading.attr="disabled">
                        Create link
                    </x-button>
                </form>
            </div>
        </div>
    @endif

    {{-- Delete confirmation modal --}}
    @if ($deletingId)
        <div class="scrim" wire:click.self="$set('deletingId', null)" x-on:keydown.escape.window="$wire.set('deletingId', null)">
            <div class="modal" style="max-width: 420px">
                <h2 class="h3" style="margin-bottom: 8px">Delete this link?</h2>
                <p class="body" style="margin-bottom: 24px">This permanently removes the short link. The short code will stop working immediately.</p>
                <div style="display: flex; justify-content: flex-end; gap: 8px">
                    <x-button variant="ghost" size="sm" wire:click="$set('deletingId', null)">Cancel</x-button>
                    <x-button variant="danger" size="sm" wire:click="delete">Delete</x-button>
                </div>
            </div>
        </div>
    @endif
</div>
