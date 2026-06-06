<?php

namespace App\Http\Controllers;

use App\Jobs\RecordClick;
use App\Models\Link;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RedirectController extends Controller
{
    /**
     * Resolve a short code and redirect, 404, or show the unavailable page.
     *
     * Active links 302-redirect to their destination; click logging is queued
     * after the response so it never delays the redirect. Disabled links show
     * a dedicated unavailable page (410 Gone) and unknown codes 404.
     */
    public function __invoke(Request $request, string $shortCode): RedirectResponse|Response
    {
        $link = Link::with('linkStatus')
            ->where('short_code', $shortCode)
            ->first();

        if ($link === null) {
            abort(404);
        }

        if (! $link->isActive) {
            return response()->view('redirect.unavailable', ['link' => $link], Response::HTTP_GONE);
        }

        RecordClick::dispatchAfterResponse(
            linkId: $link->id,
            userAgent: $request->userAgent(),
            referrer: $request->headers->get('referer'),
            ipHash: $this->hashIp($request->ip()),
        );

        return redirect()->away($link->original_url, Response::HTTP_FOUND);
    }

    /**
     * Hash the visitor IP so the raw address is never persisted.
     */
    protected function hashIp(?string $ip): ?string
    {
        if ($ip === null) {
            return null;
        }

        return hash('sha256', $ip);
    }
}
