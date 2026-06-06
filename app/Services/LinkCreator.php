<?php

namespace App\Services;

use App\Models\Link;
use App\Models\LinkStatus;
use App\Models\User;

class LinkCreator
{
    public function __construct(private ShortCodeGenerator $generator) {}

    /**
     * Create a short link for the given user, generating a code when none is supplied.
     */
    public function create(User $user, string $originalUrl, ?string $customCode = null): Link
    {
        $shortCode = filled($customCode) ? $customCode : $this->generator->generate();

        return $user->links()->create([
            'link_status_id' => LinkStatus::where('slug', 'active')->value('id'),
            'original_url' => $originalUrl,
            'short_code' => $shortCode,
            'click_count' => 0,
        ]);
    }
}
