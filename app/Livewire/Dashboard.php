<?php

namespace App\Livewire;

use App\Models\Link;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * Authenticated dashboard landing.
 *
 * Phase 5 only requires the surface for the just-created link (with
 * a copy action); the full link list and management tools land in
 * Phase 6. The shortened link id is flashed into the session by the
 * Register / Login / Shorten components when a link was just created
 * (either by the authenticated visitor directly or by materialising
 * the pending-shorten session payload).
 */
#[Layout('components.layouts.app')]
#[Title('Dashboard')]
class Dashboard extends Component
{
    /**
     * Render the dashboard, pulling any "just created" link out of the
     * session flash so it can be surfaced at the top of the page.
     */
    public function render(): View
    {
        $justCreated = null;
        $justCreatedId = session('shortened_link_id');

        if (is_int($justCreatedId) || (is_string($justCreatedId) && ctype_digit($justCreatedId))) {
            $justCreated = Link::query()
                ->where('user_id', Auth::id())
                ->whereKey((int) $justCreatedId)
                ->first();
        }

        return view('livewire.dashboard', [
            'justCreated' => $justCreated,
        ]);
    }
}
