<div class="flex flex-col gap-7" data-testid="link-detail-page">
    <header class="flex flex-col gap-2">
        <a href="{{ route('dashboard') }}" class="text-[13.5px] text-[var(--color-blue)] hover:underline inline-flex items-center gap-1">
            <span aria-hidden="true">&larr;</span> Back to dashboard
        </a>
        <span class="eyebrow">Link details</span>
        <h1 class="h1 break-all">
            <span class="mono text-[var(--color-blue)]">{{ url('/'.$link->short_code) }}</span>
        </h1>
    </header>

    <section
        class="rounded-[var(--radius-lg)] border border-[var(--color-line)] bg-white p-6 flex flex-col gap-4"
        style="box-shadow: var(--shadow-card);"
        data-testid="link-summary"
    >
        <div class="flex items-center justify-between gap-4">
            <x-ui.status-badge :status="$link->linkStatus?->slug === 'active' ? 'active' : 'disabled'" />
            <x-ui.copy-button :text="url('/'.$link->short_code)" label="Copy link" />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="flex flex-col gap-1" data-testid="detail-total-clicks">
                <span class="small text-[var(--color-ink-3)]">Total clicks</span>
                <span class="h2 mono">{{ number_format($link->click_count) }}</span>
            </div>
            <div class="flex flex-col gap-1" data-testid="detail-created-at">
                <span class="small text-[var(--color-ink-3)]">Created</span>
                <span class="body">{{ $link->created_at->format('M j, Y g:i A') }}</span>
            </div>
            <div class="flex flex-col gap-1" data-testid="detail-original-url">
                <span class="small text-[var(--color-ink-3)]">Original URL</span>
                <a href="{{ $link->original_url }}" target="_blank" rel="noopener" class="body text-[var(--color-blue)] break-all hover:underline">{{ $link->original_url }}</a>
            </div>
        </div>
    </section>

    <section
        class="rounded-[var(--radius-lg)] border border-[var(--color-line)] bg-white p-6"
        style="box-shadow: var(--shadow-card);"
        data-testid="link-chart"
    >
        <h2 class="h3 mb-4">Clicks — last 14 days</h2>

        @php
            $max = max(1, max(array_column($chartData, 'count')));
        @endphp
        <div class="flex items-end gap-2 h-[160px]" data-testid="link-chart-bars">
            @foreach ($chartData as $bucket)
                @php
                    $h = $max > 0 ? (int) round(($bucket['count'] / $max) * 140) : 0;
                @endphp
                <div class="flex-1 flex flex-col items-center gap-2">
                    <div
                        class="w-full rounded-t-[var(--radius-sm)] bg-[var(--color-blue-tint)] hover:bg-[var(--color-blue)] transition-colors"
                        style="height: {{ $h }}px; min-height: 2px;"
                        title="{{ $bucket['label'] }} — {{ $bucket['count'] }} clicks"
                        data-testid="chart-bar"
                        data-date="{{ $bucket['date']->toDateString() }}"
                        data-count="{{ $bucket['count'] }}"
                    ></div>
                    <span class="small text-[var(--color-ink-3)]">{{ $bucket['label'] }}</span>
                </div>
            @endforeach
        </div>
    </section>

    <section
        class="rounded-[var(--radius-lg)] border border-[var(--color-line)] bg-white"
        style="box-shadow: var(--shadow-card);"
        data-testid="link-click-log"
    >
        <div class="px-6 pt-6 pb-3">
            <h2 class="h3">Click log</h2>
        </div>

        @if ($clickLog->isEmpty())
            <p class="px-6 pb-6 body text-[var(--color-ink-3)]" data-testid="click-log-empty">
                No clicks yet. Share your short link to see activity here.
            </p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-[14.5px]">
                    <thead>
                        <tr class="border-b border-[var(--color-line-soft)] text-[12.5px] uppercase tracking-wide text-[var(--color-ink-3)]">
                            <th class="px-6 py-3 font-medium">When</th>
                            <th class="px-6 py-3 font-medium">Referrer</th>
                            <th class="px-6 py-3 font-medium">Browser</th>
                            <th class="px-6 py-3 font-medium">Device</th>
                        </tr>
                    </thead>
                    <tbody data-testid="click-log-rows">
                        @foreach ($clickLog as $click)
                            <tr
                                class="border-b border-[var(--color-line-soft)] last:border-0"
                                data-testid="click-log-row"
                            >
                                <td class="px-6 py-3 whitespace-nowrap" data-testid="click-log-when">
                                    {{ $click->clicked_at->format('M j, Y g:i A') }}
                                </td>
                                <td class="px-6 py-3 break-all max-w-[280px] text-[var(--color-ink-2)]" data-testid="click-log-referrer">
                                    {{ $click->referrer ?? '—' }}
                                </td>
                                <td class="px-6 py-3" data-testid="click-log-browser">
                                    {{ $click->browser?->name ?? '—' }}
                                </td>
                                <td class="px-6 py-3" data-testid="click-log-device">
                                    {{ $click->deviceType?->name ?? '—' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
</div>
