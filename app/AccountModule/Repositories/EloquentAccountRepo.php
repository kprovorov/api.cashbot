<?php

namespace App\AccountModule\Repositories;

use App\Support\Repositories\EloquentRepo;
use App\AccountModule\Models\Account;

/**
 * @extends EloquentRepo<Account>
 */
class EloquentAccountRepo extends EloquentRepo implements AccountRepo
{
    /**
     * @inheritDoc
     */
    protected function model(): string
    {
        return Account::class;
    }
}
