<?php

namespace App\LiveWire;

use App\Models\Link;
use Livewire\Component;
use Illuminate\Http\Request;

/**
 * View the details of a specific link and its click history.
 */
class LinkDetail extends Component
{
    public Link $link;

    /**
     * Number of records to display in the claim view per page (default 10).
     */
    public int $perPage = 10;

    /**
     * View the details of a specific link.
     *
     * @param string $shortCode The short code for the link.
     * @param Request $request
     * @return void
     */
    public function mount(string $shortCode, Request $request): void
    {
        $this->link = Link::where('short_code', $shortCode)->first();

        if (! $this->link) {
            abort(404);
        }

        // Ownership check: Ensure that the logged-in user owns the link.
        // If they are an admin, you might want to bypass this or check for specific permissions.
        if ($this->link->user_id !== (int)auth()->id()) {
            abort(403);
        }
    }

    /**
     * Render the livewire component's view.
     */
    public function render(): \Illuminate\View\View
    {
        return view('livewire.link-detail');
    }
}
