<?php

namespace App\PaymentModule\Repositories;

use App\Support\Repositories\EloquentRepo;
use App\PaymentModule\Models\Group;

/**
 * @extends EloquentRepo<Group>
 */
class EloquentGroupRepo extends EloquentRepo implements GroupRepo
{
    /**
     * @inheritDoc
     */
    protected function model(): string
    {
        return Group::class;
    }
}
