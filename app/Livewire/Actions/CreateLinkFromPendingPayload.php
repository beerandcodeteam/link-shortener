<?php

namespace App\Livewire\Actions;

use App\Forms\LinkForm;
use App\Models\Link;
use App\Models\LinkStatus;
use App\Models\User;

/**
 * Convert a pending shorten payload (if any) into a real Link owned by
 * the given user.
 *
 * Used as a bridge between the public homepage "Shorten" component and
 * the post-auth landing: the visitor pastes a URL before logging in, we
 * stash their intent in the session, and after they register or log in
 * we materialise the Link on their behalf.
 */
class CreateLinkFromPendingPayload
{
    /**
     * Drain the pending payload (if present) and create the Link.
     */
    public function __invoke(User $user): ?Link
    {
        $payload = session()->pull('pending_shorten');

        if (! is_array($payload) || ! isset($payload['original_url']) || ! is_string($payload['original_url'])) {
            return null;
        }

        $data = [
            'original_url' => $payload['original_url'],
            'custom_code' => $payload['custom_code'] ?? null,
        ];

        // Validate the stashed payload using the same Form Object the
        // dashboard create form uses; this guards against tampered or
        // stale session data.
        $form = LinkForm::createFrom(request()->duplicate($data));
        $form->setContainer(app())->setRedirector(app('redirect'));
        $form->validateResolved();

        $resolved = $form->resolved();

        return Link::create([
            'user_id' => $user->getKey(),
            'link_status_id' => LinkStatus::query()->where('slug', 'active')->value('id'),
            'original_url' => $resolved['original_url'],
            'short_code' => $resolved['short_code'],
            'click_count' => 0,
        ]);
    }
}
