<?php

namespace App\Livewire\Dashboard;

use App\Models\Link;
use App\Models\LinkStatus;
use Livewire\Component;

class LinkList extends Component
{
    public int $perPage = 10;

    #[Computed]
    public function links(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return auth()->user()?->links()
            ->orderByDesc('created_at')
            ->paginate($this->perPage);
    }

    /** Check if there are any links for the current user. */
    #[Computed]
    public function hasLinks(): bool
    {
        return auth()->user()?->links()->exists() ?? false;
    }

    public function toggleStatus(Link $link): void
    {
        $this->authorize('update', $link);

        $newSlug = $link->isActive() ? 'disabled' : 'active';
        $targetStatus = LinkStatus::where('slug', $newSlug)->value('id');

        $link->update(['link_status_id' => $targetStatus]);
    }

    public function render(): array
    {
        return [
            'title' => __('Links'),
        ];
    }
}
