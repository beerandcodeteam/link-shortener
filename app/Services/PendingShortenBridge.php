<?php

namespace App\Services;

use App\Models\Link;
use App\Models\User;

class PendingShortenBridge
{
    /**
     * Session key holding a guest's in-progress shorten request.
     */
    public const SESSION_KEY = 'pending_shorten';

    public function __construct(private LinkCreator $creator) {}

    /**
     * Stash a guest's shorten request so it survives the auth detour.
     */
    public function stash(string $originalUrl, ?string $customCode = null): void
    {
        session()->put(self::SESSION_KEY, [
            'original_url' => $originalUrl,
            'custom_code' => filled($customCode) ? $customCode : null,
        ]);
    }

    /**
     * Determine whether a pending shorten request exists in the session.
     */
    public function has(): bool
    {
        return session()->has(self::SESSION_KEY);
    }

    /**
     * Create the pending link for the user (if any) and clear the payload.
     */
    public function fulfill(User $user): ?Link
    {
        $payload = session()->pull(self::SESSION_KEY);

        if (! $payload) {
            return null;
        }

        return $this->creator->create($user, $payload['original_url'], $payload['custom_code'] ?? null);
    }
}
