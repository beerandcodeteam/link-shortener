<?php

namespace App\Http\Controllers;

use App\Actions\RecordClick;
use App\Models\Link;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RedirectController extends Controller
{
    /**
     * Handle a short-link redirect.
     *
     * @param  string  $shortCode  The short code segment from the URL.
     */
    public function __invoke(string $shortCode): RedirectResponse|View
    {
        $link = Link::where('short_code', $shortCode)->first();

        if (! $link) {
            abort(404);
        }

        if ($link->linkStatus?->slug !== 'active') {
            return view('pages.link-unavailable');
        }

        // Record click before redirect (counter + per-click log)
        app(RecordClick::class)->handle($link, request());

        return redirect($link->original_url, 302);
    }
}
