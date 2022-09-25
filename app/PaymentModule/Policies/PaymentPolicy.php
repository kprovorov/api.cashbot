<?php

namespace App\PaymentModule\Policies;

use App\Models\User;
use App\PaymentModule\Models\Payment;
use Illuminate\Auth\Access\HandlesAuthorization;

class PaymentPolicy
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
     * @param  Payment  $payment
     * @return bool
     */
    public function view(User $user, Payment $payment): bool
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
     * @param  Payment  $payment
     * @return bool
     */
    public function update(User $user, Payment $payment): bool
    {
        return true;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param  Payment  $payment
     * @return bool
     */
    public function delete(User $user, Payment $payment): bool
    {
        return true;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  User  $user
     * @param  Payment  $payment
     * @return bool
     */
    public function restore(User $user, Payment $payment): bool
    {
        return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  User  $user
     * @param  Payment  $payment
     * @return bool
     */
    public function forceDelete(User $user, Payment $payment): bool
    {
        return true;
    }
}
