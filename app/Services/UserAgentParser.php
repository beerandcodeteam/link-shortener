<?php

namespace App\Services;

use Database\Seeders\LookupSeeder;

/**
 * Maps a raw User-Agent string to the seeded device-type and browser slugs.
 *
 * Lightweight heuristic parser — intentionally dependency-free. The returned
 * slugs always match a row seeded by {@see LookupSeeder}.
 */
class UserAgentParser
{
    /**
     * Resolve the device-type slug for the given user agent.
     *
     * Returns one of: bot, tablet, mobile, desktop, unknown.
     */
    public function deviceSlug(?string $userAgent): string
    {
        if ($userAgent === null || trim($userAgent) === '') {
            return 'unknown';
        }

        $ua = strtolower($userAgent);

        if ($this->matchesAny($ua, ['bot', 'crawler', 'spider', 'curl', 'wget', 'slurp', 'facebookexternalhit'])) {
            return 'bot';
        }

        if ($this->matchesAny($ua, ['ipad', 'tablet', 'kindle', 'playbook', 'silk'])) {
            return 'tablet';
        }

        if ($this->matchesAny($ua, ['mobi', 'iphone', 'ipod', 'android', 'blackberry', 'windows phone'])) {
            return 'mobile';
        }

        return 'desktop';
    }

    /**
     * Resolve the browser slug for the given user agent.
     *
     * Returns one of: edge, chrome, firefox, safari, other.
     */
    public function browserSlug(?string $userAgent): string
    {
        if ($userAgent === null || trim($userAgent) === '') {
            return 'other';
        }

        $ua = strtolower($userAgent);

        if (str_contains($ua, 'edg')) {
            return 'edge';
        }

        if ($this->matchesAny($ua, ['firefox', 'fxios'])) {
            return 'firefox';
        }

        if ($this->matchesAny($ua, ['chrome', 'crios', 'chromium'])) {
            return 'chrome';
        }

        if (str_contains($ua, 'safari')) {
            return 'safari';
        }

        return 'other';
    }

    /**
     * Determine whether the haystack contains any of the given needles.
     *
     * @param  array<int, string>  $needles
     */
    protected function matchesAny(string $haystack, array $needles): bool
    {
        foreach ($needles as $needle) {
            if (str_contains($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }
}
