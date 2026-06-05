<?php

namespace App\Http\Controllers;

use App\Actions\RecordClick;
use App\Models\Link;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Public catch-all handler for short codes.
 *
 * Active links redirect (302) to the original URL and dispatch a click
 * recording job deferred until after the response is sent. Missing codes
 * 404. Disabled codes render a dedicated "link unavailable" page so the
 * visitor is never silently redirected to a stale destination.
 */
class RedirectController extends Controller
{
    /**
     * Resolve and act on the short code.
     */
    public function __invoke(Request $request, string $shortCode)
    {
        $link = Link::query()->where('short_code', $shortCode)->first();

        if ($link === null) {
            abort(404);
        }

        if (! $link->is_active) {
            return response()->view('errors.link-unavailable', [
                'link' => $link,
            ]);
        }

        // Dispatch the click recording as a terminating callback so the
        // logging work does not block the redirect response. We use the
        // namespaced helper directly because the global `defer()` function is
        // shadowed by the Swoole coroutine API in this PHP build.
        \Illuminate\Support\defer(fn () => app(RecordClick::class)($link, $request));

        return redirect($link->original_url, Response::HTTP_FOUND);
    }
}
