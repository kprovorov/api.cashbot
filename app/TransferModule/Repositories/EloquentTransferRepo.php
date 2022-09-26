<?php

namespace App\TransferModule\Repositories;

use App\Support\Repositories\EloquentRepo;
use App\TransferModule\Models\Transfer;

/**
 * @extends EloquentRepo<Transfer>
 */
class EloquentTransferRepo extends EloquentRepo implements TransferRepo
{
    /**
     * {@inheritDoc}
     */
    protected function model(): string
    {
        return Transfer::class;
    }
}
