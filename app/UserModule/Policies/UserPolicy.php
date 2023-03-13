<?php

namespace App\UserModule\Policies;

use App\UserModule\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function index(User $actingUser): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $actingUser, User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $actingUser): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $actingUser, User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $actingUser, User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $actingUser, User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $actingUser, User $user): bool
    {
        return true;
    }
}
