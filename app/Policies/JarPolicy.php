<?php

namespace App\Policies;

use App\Models\Jar;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class JarPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): \Illuminate\Auth\Access\Response|bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Jar $jar): \Illuminate\Auth\Access\Response|bool
    {
        //
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): \Illuminate\Auth\Access\Response|bool
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Jar $jar): \Illuminate\Auth\Access\Response|bool
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Jar $jar): \Illuminate\Auth\Access\Response|bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Jar $jar): \Illuminate\Auth\Access\Response|bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Jar $jar): \Illuminate\Auth\Access\Response|bool
    {
        //
    }
}
