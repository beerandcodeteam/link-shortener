<?php

namespace App\Policies;

use App\Models\Link;
use App\Models\User;

/**
 * Ownership policy for {@see Link} records.
 *
 * The dashboard, detail, toggle and delete actions are all gated by these
 * methods so a user can only ever touch links they own. The public
 * redirect route intentionally bypasses this policy — short codes are
 * meant to be shareable.
 */
class LinkPolicy
{
    /**
     * Determine whether the user can view the link.
     */
    public function view(User $user, Link $link): bool
    {
        return $this->owns($user, $link);
    }

    /**
     * Determine whether the user can update the link (toggle, edit).
     */
    public function update(User $user, Link $link): bool
    {
        return $this->owns($user, $link);
    }

    /**
     * Determine whether the user can delete the link.
     */
    public function delete(User $user, Link $link): bool
    {
        return $this->owns($user, $link);
    }

    /**
     * Shared ownership check.
     */
    protected function owns(User $user, Link $link): bool
    {
        return (int) $link->user_id === (int) $user->getKey();
    }
}
