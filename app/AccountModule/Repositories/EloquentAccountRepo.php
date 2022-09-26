<?php

namespace App\AccountModule\Repositories;

use App\AccountModule\Models\Account;
use App\Support\Repositories\EloquentRepo;

/**
 * @extends EloquentRepo<Account>
 */
class EloquentAccountRepo extends EloquentRepo implements AccountRepo
{
    /**
     * {@inheritDoc}
     */
    protected function model(): string
    {
        return Account::class;
    }
}
