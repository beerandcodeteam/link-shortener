<div class="wrap" style="width: 100%">
    <a class="nav-link" href="{{ route('dashboard') }}" wire:navigate style="display: inline-flex; align-items: center; gap: 6px; margin-bottom: 20px">
        <x-icon name="back" :size="16" /> Back to links
    </a>

    {{-- Summary --}}
    <div class="card" style="padding: 24px; margin-bottom: 24px">
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px">
            <a class="mono h3" href="{{ route('redirect', $link->short_code) }}" target="_blank">{{ route('redirect', $link->short_code) }}</a>
            <x-status-badge :status="$link->isActive ? 'active' : 'disabled'" />
            <x-copy-button :text="route('redirect', $link->short_code)" label="Copy" />
        </div>

        <p class="small" style="color: var(--ink-3)">Destination</p>
        <a class="body" href="{{ $link->original_url }}" target="_blank" style="display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; margin-bottom: 16px">{{ $link->original_url }}</a>

        <div style="display: flex; gap: 32px">
            <div>
                <p class="small" style="color: var(--ink-3)">Total clicks</p>
                <p class="h2">{{ $link->click_count }}</p>
            </div>
            <div>
                <p class="small" style="color: var(--ink-3)">Created</p>
                <p class="body" style="font-weight: 600">{{ $link->created_at->format('M j, Y') }}</p>
            </div>
        </div>
    </div>

    {{-- Clicks over time --}}
    <div class="card" style="padding: 24px; margin-bottom: 24px">
        <p class="small" style="color: var(--ink-3); margin-bottom: 16px">Clicks over the last {{ $chartDays }} days</p>
        <div style="display: flex; align-items: flex-end; gap: 5px; height: 120px">
            @foreach ($buckets as $bucket)
                <div style="flex: 1; display: flex; flex-direction: column; justify-content: flex-end; height: 100%" title="{{ $bucket['label'] }}: {{ $bucket['count'] }} clicks">
                    <div style="height: {{ ($bucket['count'] / $maxCount) * 100 }}%; min-height: {{ $bucket['count'] > 0 ? 4 : 2 }}px; background: {{ $bucket['count'] > 0 ? 'var(--blue)' : 'var(--line)' }}; border-radius: 4px"></div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Per-click log --}}
    <div class="card" style="padding: 24px">
        <p class="small" style="color: var(--ink-3); margin-bottom: 16px">Click log</p>

        @if ($clicks->isEmpty())
            <p class="body">No clicks recorded yet.</p>
        @else
            <table style="width: 100%; border-collapse: collapse">
                <thead>
                    <tr style="text-align: left; border-bottom: 1px solid var(--line)">
                        <th class="small" style="padding: 8px 12px 8px 0; font-weight: 600">Date &amp; time</th>
                        <th class="small" style="padding: 8px 12px; font-weight: 600">Referrer</th>
                        <th class="small" style="padding: 8px 12px; font-weight: 600">Browser</th>
                        <th class="small" style="padding: 8px 0 8px 12px; font-weight: 600">Device</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($clicks as $click)
                        <tr wire:key="click-{{ $click->id }}" style="border-bottom: 1px solid var(--line)">
                            <td class="small" style="padding: 10px 12px 10px 0">{{ $click->clicked_at->format('M j, Y g:i A') }}</td>
                            <td class="small" style="padding: 10px 12px; overflow: hidden; text-overflow: ellipsis; max-width: 220px; white-space: nowrap">{{ $click->referrer ?? 'Direct' }}</td>
                            <td class="small" style="padding: 10px 12px">{{ $click->browser?->name ?? 'Unknown' }}</td>
                            <td class="small" style="padding: 10px 0 10px 12px">{{ $click->deviceType?->name ?? 'Unknown' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
