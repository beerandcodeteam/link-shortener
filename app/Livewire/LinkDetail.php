<?php

namespace App\Livewire;

use App\Models\Click;
use App\Models\Link;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class LinkDetail extends Component
{
    public Link $link;

    /**
     * Number of days shown in the clicks-over-time chart.
     */
    public int $chartDays = 14;

    public function mount(Link $link): void
    {
        $this->authorize('view', $link);

        $this->link = $link;
    }

    /**
     * Build the daily click buckets for the bar chart, oldest day first.
     *
     * @param  Collection<int, Click>  $clicks
     * @return array<int, array{label: string, count: int}>
     */
    protected function chartBuckets(Collection $clicks): array
    {
        $start = Carbon::today()->subDays($this->chartDays - 1);

        $counts = $clicks
            ->filter(fn ($click) => $click->clicked_at->gte($start))
            ->groupBy(fn ($click) => $click->clicked_at->toDateString())
            ->map->count();

        $buckets = [];

        for ($i = 0; $i < $this->chartDays; $i++) {
            $day = $start->copy()->addDays($i);

            $buckets[] = [
                'label' => $day->format('M j'),
                'count' => (int) ($counts[$day->toDateString()] ?? 0),
            ];
        }

        return $buckets;
    }

    public function render(): View
    {
        $clicks = $this->link->clicks()
            ->with(['deviceType', 'browser'])
            ->latest('clicked_at')
            ->get();

        $buckets = $this->chartBuckets($clicks);

        return view('livewire.link-detail', [
            'clicks' => $clicks,
            'buckets' => $buckets,
            'maxCount' => max(1, ...array_map(fn ($b) => $b['count'], $buckets)),
        ]);
    }
}
