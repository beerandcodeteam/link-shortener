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

        if ($link->status->is_active) {
            return redirect()->away($link->original_url, 302);
        }

        // If the link doesn't exist or is not active, we check if it's disabled specifically.
        if ($link && !$link->status->is_active) {
            return view('errors.unavailable', ['shortCode' => $shortCode]);
        }

        abort(404);
    }
}
