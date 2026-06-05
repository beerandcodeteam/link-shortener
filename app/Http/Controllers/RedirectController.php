<?php

namespace App\Http\Controllers;

use App\Models\Link;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;

class RedirectController extends Controller
{
    /**
     * Handle the redirection of a link using its short code.
     *
     * @param string $shortCode
     * @return RedirectResponse|Response
     */
    public function handle(string $shortCode): RedirectResponse|Response
    {
        $link = Link::where('short_code', $shortCode)->first();

        if (!$link) {
            abort(404);
        }

        if ($link->status === 'disabled') {
            // Return a dedicated "link unavailable" page (not a redirect).
            // This matches the requirement for 4.1.2
            return view('errors.unavailable', ['shortCode' => $shortCode]);
        }

        // Active link -> 302 redirect to original_url
        if ($link->original_url) {
            return redirect()->away($link->original_url, 302);
        }

        abort(404);
    }
}
