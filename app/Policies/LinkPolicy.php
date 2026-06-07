<?php

namespace App\Policies;

use App\Models\Link;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LinkPolicy
{
    /**
     * Determine whether the user can view any links.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the link.
     */
    public function view(User $user, Link $link): bool
    {
        return $this->ownsLink($user, $link);
    }

    /**
     * Determine whether the user can create links.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the link.
     */
    public function update(User $user, Link $link): bool
    {
        return $this->ownsLink($user, $link);
    }

    /**
     * Determine whether the user can delete the link.
     */
    public function delete(User $user, Link $link): bool
    {
        return $this->ownsLink($user, $link);
    }

    /**
     * Determine whether the user can restore the link.
     */
    public function restore(User $user, Link $link): bool
    {
        return $this->ownsLink($user, $link);
    }

    /**
     * Determine whether the user can permanently delete the link.
     */
    public function forceDelete(User $user, Link $link): bool
    {
        return $this->ownsLink($user, $link);
    }

    /**
     * Check if the user owns the given link.
     */
    protected function ownsLink(User $user, Link $link): bool
    {
        return $link->user_id === $user->id;
    }
}
