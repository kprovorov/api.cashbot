<?php

namespace App\PaymentModule\Repositories;

use App\PaymentModule\Models\Group;
use App\Support\Repositories\EloquentRepo;

/**
 * @extends EloquentRepo<Group>
 */
class EloquentGroupRepo extends EloquentRepo implements GroupRepo
{
    /**
     * {@inheritDoc}
     */
    protected function model(): string
    {
        return Group::class;
    }
}
