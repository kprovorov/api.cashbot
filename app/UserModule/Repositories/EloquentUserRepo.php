<?php

namespace App\UserModule\Repositories;

use App\Support\Repositories\EloquentRepo;
use App\UserModule\Models\User;

/**
 * @extends EloquentRepo<User>
 */
class EloquentUserRepo extends EloquentRepo implements UserRepo
{
    /**
     * @inheritDoc
     */
    protected function model(): string
    {
        return User::class;
    }
}
