<?php

namespace App\Livewire;

use App\Models\Link;
use App\Policies\LinkPolicy;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * Authenticated link detail view.
 *
 * Shows a single link's full information (original URL, short URL,
 * status, creation date, total clicks) along with a bar chart of click
 * volume over the last 14 days and a per-click log table.
 *
 * Authorization: the parent route is gated with the `view` policy
 * method (see {@see LinkPolicy}); if the user is not the
 * owner the route never reaches this component.
 */
#[Layout('components.layouts.app')]
#[Title('Link details')]
class LinkDetail extends Component
{
    public Link $link;

    /**
     * Hydrate the component with the resolved Link model.
     */
    public function mount(Link $link): void
    {
        $this->link = $link->load(['linkStatus', 'clicks.deviceType', 'clicks.browser']);
    }

    /**
     * Per-day click counts for the last 14 days, oldest first.
     *
     * @return array<int, array{date: CarbonImmutable, label: string, count: int}>
     */
    public function chartData(): array
    {
        $start = now()->subDays(13)->startOfDay();
        $end = now()->endOfDay();

        $counts = $this->link->clicks()
            ->whereBetween('clicked_at', [$start, $end])
            ->selectRaw('date_trunc(\'day\', clicked_at) as day, count(*) as c')
            ->groupBy('day')
            ->pluck('c', 'day');

        $data = [];
        for ($i = 0; $i < 14; $i++) {
            $day = $start->copy()->addDays($i);
            $key = $day->toDateString();
            $data[] = [
                'date' => $day,
                'label' => $day->format('M j'),
                'count' => (int) ($counts[$key] ?? 0),
            ];
        }

        return $data;
    }

    /**
     * Recent click log entries, newest first.
     */
    public function clickLog(): Collection
    {
        return $this->link->clicks()
            ->with(['deviceType', 'browser'])
            ->orderByDesc('clicked_at')
            ->limit(50)
            ->get();
    }

    public function render(): View
    {
        return view('livewire.link-detail', [
            'chartData' => $this->chartData(),
            'clickLog' => $this->clickLog(),
        ]);
    }
}
