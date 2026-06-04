<?php

namespace App\Actions;

use App\Models\Browser;
use App\Models\Click;
use App\Models\DeviceType;
use App\Models\Link;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Persist a single click event for a link.
 *
 * Responsibilities:
 *  - Resolve a `device_type_id` and `browser_id` from the user-agent.
 *  - Hash the visitor IP (never store the raw value).
 *  - Increment `links.click_count` and insert a row into `clicks`.
 *
 * Intended to be invoked via `defer()` so logging does not delay the
 * redirect.
 */
class RecordClick
{
    /**
     * Record the click against the given link.
     */
    public function __invoke(Link $link, ?Request $request = null): ?Click
    {
        $request ??= request();

        $deviceTypeId = $this->resolveDeviceTypeId($request->userAgent());
        $browserId = $this->resolveBrowserId($request->userAgent());
        $referrer = $this->resolveReferrer($request);
        $ipHash = $this->hashIp($request->ip());

        $clickedAt = now();

        return DB::transaction(function () use ($link, $deviceTypeId, $browserId, $referrer, $ipHash, $clickedAt): Click {
            $link->newQuery()
                ->whereKey($link->getKey())
                ->update([
                    'click_count' => DB::raw('click_count + 1'),
                ]);

            $link->refresh();

            return $link->clicks()->create([
                'device_type_id' => $deviceTypeId,
                'browser_id' => $browserId,
                'referrer' => $referrer,
                'ip_hash' => $ipHash,
                'clicked_at' => $clickedAt,
            ]);
        });
    }

    /**
     * Resolve a `device_type_id` from the given user-agent string.
     */
    protected function resolveDeviceTypeId(?string $userAgent): ?int
    {
        $slug = $this->deviceSlugFor($userAgent);

        if ($slug === null) {
            return null;
        }

        $id = DeviceType::query()->where('slug', $slug)->value('id');

        return $id !== null ? (int) $id : null;
    }

    /**
     * Resolve a `browser_id` from the given user-agent string.
     */
    protected function resolveBrowserId(?string $userAgent): ?int
    {
        $slug = $this->browserSlugFor($userAgent);

        $id = Browser::query()->where('slug', $slug)->value('id');

        return $id !== null ? (int) $id : null;
    }

    /**
     * Map a user-agent to a device-type slug from the lookup table.
     */
    protected function deviceSlugFor(?string $userAgent): string
    {
        $ua = strtolower($userAgent ?? '');

        if ($ua === '') {
            return 'unknown';
        }

        if (str_contains($ua, 'bot') || str_contains($ua, 'crawl') || str_contains($ua, 'spider')) {
            return 'bot';
        }

        if (str_contains($ua, 'ipad') || str_contains($ua, 'tablet') || str_contains($ua, 'playbook')) {
            return 'tablet';
        }

        if (str_contains($ua, 'mobi') || str_contains($ua, 'iphone') || str_contains($ua, 'ipod') || str_contains($ua, 'android')) {
            // Android tablets spoof "Android" without "Mobi" - we already handled tablets above.
            return 'mobile';
        }

        if (str_contains($ua, 'windows') || str_contains($ua, 'macintosh') || str_contains($ua, 'linux') || str_contains($ua, 'x11')) {
            return 'desktop';
        }

        return 'unknown';
    }

    /**
     * Map a user-agent to a browser slug from the lookup table.
     */
    protected function browserSlugFor(?string $userAgent): string
    {
        $ua = strtolower($userAgent ?? '');

        if ($ua === '') {
            return 'other';
        }

        // Order matters: Edge contains "Chrome", Chrome contains "Safari".
        if (str_contains($ua, 'edg/') || str_contains($ua, 'edge/')) {
            return 'edge';
        }

        if (str_contains($ua, 'firefox') || str_contains($ua, 'fxios')) {
            return 'firefox';
        }

        if (str_contains($ua, 'safari') && ! str_contains($ua, 'chrome') && ! str_contains($ua, 'chromium')) {
            return 'safari';
        }

        if (str_contains($ua, 'chrome') || str_contains($ua, 'chromium')) {
            return 'chrome';
        }

        return 'other';
    }

    /**
     * Pull a sanitized referrer from the request, or null when missing.
     */
    protected function resolveReferrer(Request $request): ?string
    {
        $referrer = $request->headers->get('referer');

        if (! is_string($referrer)) {
            return null;
        }

        $referrer = trim($referrer);

        if ($referrer === '') {
            return null;
        }

        // Cap referrer length to fit the column and prevent abuse.
        return mb_substr($referrer, 0, 2048);
    }

    /**
     * Hash the visitor IP so the raw value is never persisted.
     */
    protected function hashIp(?string $ip): ?string
    {
        if (! is_string($ip) || $ip === '') {
            return null;
        }

        return Hash::make($ip);
    }
}
