<?php

namespace App\UserModule\Policies;

use App\UserModule\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param User $actingUser
     * @return bool
     */
    public function index(User $actingUser): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $actingUser
     * @param User $user
     * @return bool
     */
    public function view(User $actingUser, User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $actingUser
     * @return bool
     */
    public function create(User $actingUser): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $actingUser
     * @param User $user
     * @return bool
     */
    public function update(User $actingUser, User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $actingUser
     * @param User $user
     * @return bool
     */
    public function delete(User $actingUser, User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $actingUser
     * @param User $user
     * @return bool
     */
    public function restore(User $actingUser, User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $actingUser
     * @param User $user
     * @return bool
     */
    public function forceDelete(User $actingUser, User $user): bool
    {
        return true;
    }
}
