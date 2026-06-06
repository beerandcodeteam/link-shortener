<?php

namespace App\Actions;

use App\Jobs\RecordClick as RecordClickJob;
use App\Models\Link;
use Illuminate\Http\Request;

/**
 * Dispatch click recording so it does not block the redirect response.
 */
class RecordClick
{
    /**
     * Record a click for the given link and request (non-blocking).
     */
    public function handle(Link $link, Request $request): void
    {
        RecordClickJob::dispatch(
            $link->id,
            $request->header('User-Agent', ''),
            $request->header('Referer', $request->header('Referrer', '')),
            hash('sha256', $request->ip() ?? ''),
        )->afterResponse();
    }
}
