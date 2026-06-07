<?php

namespace App\Livewire\Dashboard;

use Livewire\Attributes\Computed;
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

    public function render(): array
    {
        return [
            'title' => __('Links'),
        ];
    }
}
