<?php

namespace App\Http\Controllers;

use App\Models\Link;
use App\Models\LinkStatus;
use App\Policies\LinkPolicy;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Synchronous actions that own a single link: toggle its status and
 * delete it. Authorization is enforced by the {@see LinkPolicy}
 * via route middleware (see `routes/web.php`).
 */
class LinkController extends Controller
{
    /**
     * Toggle the link between active and disabled.
     */
    public function toggle(Request $request, Link $link): RedirectResponse
    {
        $activeStatusId = LinkStatus::query()->where('slug', 'active')->value('id');
        $disabledStatusId = LinkStatus::query()->where('slug', 'disabled')->value('id');

        $isActive = $link->linkStatus?->slug === 'active';

        $link->forceFill([
            'link_status_id' => $isActive ? $disabledStatusId : $activeStatusId,
        ])->save();

        session()->flash('toast', $isActive ? 'Link disabled.' : 'Link enabled.');

        return back();
    }

    /**
     * Hard-delete the link.
     */
    public function destroy(Request $request, Link $link): RedirectResponse
    {
        $link->delete();

        session()->flash('toast', 'Link deleted.');

        return redirect()->route('dashboard');
    }
}
