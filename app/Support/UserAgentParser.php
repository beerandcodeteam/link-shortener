<?php

namespace App\Support;

use App\Models\Browser;
use App\Models\DeviceType;

/**
 * Parse user-agent strings into device and browser lookup IDs.
 */
class UserAgentParser
{
    /**
     * Resolve device type slug from a user-agent string.
     */
    public static function parseDeviceType(string $userAgent): ?string
    {
        // Bots detected first (high-priority patterns)
        if (preg_match('/(bot|crawl|spider|slurp|ia_archiver)/i', $userAgent)) {
            return 'bot';
        }

        $lower = strtolower($userAgent);

        // Tablet: iPad, "; tablet;" token, or "Tablet;" anywhere
        if (str_contains($lower, 'ipad')
            || str_contains($lower, '; tablet;')
            || preg_match('/tablet(?! pc)/i', $userAgent)
        ) {
            return 'tablet';
        }

        // Mobile: iPhones, Android mobile devices, and explicit mobile tokens
        if (preg_match('/(iphone|android.*mobile|iemobile|mobile)/i', $userAgent)) {
            return 'mobile';
        }

        // Desktop is the default for all non-mobile/bot/tablet clients
        return 'desktop';
    }

    /**
     * Resolve browser slug from a user-agent string.
     */
    public static function parseBrowser(string $userAgent): ?string
    {
        // Edge must be checked before Chrome (Edge contains "Chrome")
        if (preg_match('/edg[eos?\/]/i', $userAgent)) {
            return 'edge';
        }

        // Safari: contains "Safari/" but NOT "Chrome" or "CriOS"
        if (preg_match('/safari\/[\d.]+/i', $userAgent)
            && ! preg_match('/(chrome|chromium|crios)/i', $userAgent)
        ) {
            return 'safari';
        }

        // Chrome / Chromium-based browsers
        if (preg_match('/(chrome|chromium|crios)/i', $userAgent)) {
            return 'chrome';
        }

        // Firefox with version number
        if (preg_match('/firefox\/[\d.]+/i', $userAgent)) {
            return 'firefox';
        }

        return 'other';
    }

    /**
     * Get the device type ID matching the user-agent, creating a lookup row if missing.
     */
    public static function resolveDeviceTypeId(string $userAgent): ?int
    {
        $slug = static::parseDeviceType($userAgent);

        return DeviceType::firstOrCreate(
            ['slug' => $slug],
            ['name' => ucwords(str_replace('_', ' ', $slug))]
        )->id;
    }

    /**
     * Get the browser ID matching the user-agent, creating a lookup row if missing.
     */
    public static function resolveBrowserId(string $userAgent): ?int
    {
        $slug = static::parseBrowser($userAgent);

        return Browser::firstOrCreate(
            ['slug' => $slug],
            ['name' => ucwords(str_replace('_', ' ', $slug))]
        )->id;
    }
}
