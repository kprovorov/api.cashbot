---
to: app/<%= h.changeCase.pascal(h.inflection.singularize(module)) %>Module/Policies/<%= h.changeCase.pascal(h.inflection.singularize(name)) %>Policy.php
---
<?php

namespace App\<%= h.changeCase.pascal(h.inflection.singularize(module)) %>Module\Policies;

use App\<%= h.changeCase.pascal(h.inflection.singularize(module)) %>Module\Models\<%= h.changeCase.pascal(h.inflection.singularize(name)) %>;
use App\UserModule\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class <%= h.changeCase.pascal(h.inflection.singularize(name)) %>Policy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param User $user
     * @return bool
     */
    public function index(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param <%= h.changeCase.pascal(h.inflection.singularize(name)) %> $<%= h.changeCase.camel(h.inflection.singularize(name)) %>
     * @return bool
     */
    public function view(User $user, <%= h.changeCase.pascal(h.inflection.singularize(name)) %> $<%= h.changeCase.camel(h.inflection.singularize(name)) %>): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param <%= h.changeCase.pascal(h.inflection.singularize(name)) %> $<%= h.changeCase.camel(h.inflection.singularize(name)) %>
     * @return bool
     */
    public function update(User $user, <%= h.changeCase.pascal(h.inflection.singularize(name)) %> $<%= h.changeCase.camel(h.inflection.singularize(name)) %>): bool
    {
        return true;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param <%= h.changeCase.pascal(h.inflection.singularize(name)) %> $<%= h.changeCase.camel(h.inflection.singularize(name)) %>
     * @return bool
     */
    public function delete(User $user, <%= h.changeCase.pascal(h.inflection.singularize(name)) %> $<%= h.changeCase.camel(h.inflection.singularize(name)) %>): bool
    {
        return true;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param <%= h.changeCase.pascal(h.inflection.singularize(name)) %> $<%= h.changeCase.camel(h.inflection.singularize(name)) %>
     * @return bool
     */
    public function restore(User $user, <%= h.changeCase.pascal(h.inflection.singularize(name)) %> $<%= h.changeCase.camel(h.inflection.singularize(name)) %>): bool
    {
        return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param <%= h.changeCase.pascal(h.inflection.singularize(name)) %> $<%= h.changeCase.camel(h.inflection.singularize(name)) %>
     * @return bool
     */
    public function forceDelete(User $user, <%= h.changeCase.pascal(h.inflection.singularize(name)) %> $<%= h.changeCase.camel(h.inflection.singularize(name)) %>): bool
    {
        return true;
    }
}
