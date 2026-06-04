<?php

namespace App\Livewire\Actions;

use App\Forms\LinkForm;
use App\Models\Link;
use App\Models\LinkStatus;
use App\Models\User;

/**
 * Resolve a short-code form submission into a concrete Link owned by
 * the given user. Wraps the {@see LinkForm} validation rules so the
 * same constraints (URL format, scheme, custom code charset/length,
 * uniqueness, reserved words) apply on the public homepage, the
 * dashboard create form, and the post-auth bridge from the
 * pending-shorten payload.
 */
class CreateLinkForUser
{
    /**
     * Build the form-request from a plain data array, validate it, and
     * persist the resulting Link owned by the given user.
     */
    public function __invoke(User $user, array $data): Link
    {
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
