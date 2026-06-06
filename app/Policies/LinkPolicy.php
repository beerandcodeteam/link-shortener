<?php

namespace App\Policies;

use App\Models\Link;
use App\Models\User;

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
     * Determine whether the user can update the link.
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
     * Determine whether the user owns the link.
     */
    protected function owns(User $user, Link $link): bool
    {
        return $user->id === $link->user_id;
    }
}
