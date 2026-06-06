<?php

namespace App\Actions;

use App\Models\Click;
use App\Models\Link;
use App\Support\UserAgentParser;
use Illuminate\Http\Request;

/**
 * Increment a link's click count and record the per-click log entry.
 */
class RecordClick
{
    /**
     * Record a click for the given link and request.
     */
    public function handle(Link $link, Request $request): void
    {
        // Increment the counter atomically on the Link model
        $link->increment('click_count');

        // Resolve device type and browser from user-agent
        $userAgent = $request->header('User-Agent', '');

        Click::create([
            'link_id' => $link->id,
            'device_type_id' => UserAgentParser::resolveDeviceTypeId($userAgent),
            'browser_id' => UserAgentParser::resolveBrowserId($userAgent),
            'referrer' => $request->header('Referer', $request->header('Referrer', '')),
            'ip_hash' => hash('sha256', $request->ip() ?? ''),
            'clicked_at' => now(),
        ]);
    }
}
