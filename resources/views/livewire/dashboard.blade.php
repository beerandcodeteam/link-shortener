<div class="wrap" style="width: 100%">
    <h1 class="h2" style="margin-bottom: 24px">Your links</h1>

    @if ($createdLink)
        <div class="card" style="padding: 20px; margin-bottom: 24px; border-color: var(--green); display: flex; align-items: center; justify-content: space-between; gap: 16px">
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
            <div style="min-width: 0">
                <a class="mono" href="{{ route('redirect', $link->short_code) }}" target="_blank">{{ $link->short_code }}</a>
                <p class="small" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap">{{ $link->original_url }}</p>
            </div>
            <div style="display: flex; align-items: center; gap: 12px; flex-shrink: 0">
                <span class="small">{{ $link->click_count }} clicks</span>
                <x-copy-button :text="route('redirect', $link->short_code)" />
            </div>
        </div>
    @empty
        <p class="body">No links yet. <a href="{{ url('/') }}" wire:navigate>Create your first one</a>.</p>
    @endforelse
</div>
