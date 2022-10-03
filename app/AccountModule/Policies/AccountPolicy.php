<?php

namespace App\AccountModule\Policies;

use App\AccountModule\Models\Account;
use App\UserModule\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AccountPolicy
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
     * @param  Account  $account
     * @return bool
     */
    public function view(User $user, Account $account): bool
    {
        return $account->user_id === $user->id;
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
     * @param  Account  $account
     * @return bool
     */
    public function update(User $user, Account $account): bool
    {
        return $account->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param  Account  $account
     * @return bool
     */
    public function delete(User $user, Account $account): bool
    {
        return $account->user_id === $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  User  $user
     * @param  Account  $account
     * @return bool
     */
    public function restore(User $user, Account $account): bool
    {
        return $account->user_id === $user->id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  User  $user
     * @param  Account  $account
     * @return bool
     */
    public function forceDelete(User $user, Account $account): bool
    {
        return $account->user_id === $user->id;
    }
}
