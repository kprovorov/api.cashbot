<?php

namespace App\TransferModule\Policies;

use App\Models\User;
use App\TransferModule\Models\Transfer;
use Illuminate\Auth\Access\HandlesAuthorization;

class TransferPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  User  $user
     * @return bool
     */
    public function index(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  User  $user
     * @param  Transfer  $transfer
     * @return bool
     */
    public function view(User $user, Transfer $transfer): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  User  $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param  Transfer  $transfer
     * @return bool
     */
    public function update(User $user, Transfer $transfer): bool
    {
        return true;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param  Transfer  $transfer
     * @return bool
     */
    public function delete(User $user, Transfer $transfer): bool
    {
        return true;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  User  $user
     * @param  Transfer  $transfer
     * @return bool
     */
    public function restore(User $user, Transfer $transfer): bool
    {
        return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  User  $user
     * @param  Transfer  $transfer
     * @return bool
     */
    public function forceDelete(User $user, Transfer $transfer): bool
    {
        return true;
    }
}
