<?php

namespace App\Livewire\Dashboard;

use App\Models\Link;
use App\Models\LinkStatus;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Livewire\Component;

class LinkList extends Component
{
    public int $perPage = 10;

    public bool $showDeleteModal = false;

    public ?int $deletingLinkId = null;

    #[Computed]
    public function links(): LengthAwarePaginator
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

    public function confirmDelete(Link $link): void
    {
        $this->authorize('delete', $link);

        $this->deletingLinkId = $link->id;
        $this->showDeleteModal = true;
    }

    public function executeDelete(): void
    {
        $link = Link::find($this->deletingLinkId);

        abort_if(is_null($link), 404);

        $this->authorize('delete', $link);

        $link->delete();

        $this->showDeleteModal = false;
        $this->deletingLinkId = null;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->deletingLinkId = null;
    }

    public function render(): array
    {
        return [
            'title' => __('Links'),
        ];
    }
}
