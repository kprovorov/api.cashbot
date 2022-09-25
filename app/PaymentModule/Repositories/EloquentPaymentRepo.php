<?php

namespace App\PaymentModule\Repositories;

use App\Support\Repositories\EloquentRepo;
use App\PaymentModule\Models\Payment;

/**
 * @extends EloquentRepo<Payment>
 */
class EloquentPaymentRepo extends EloquentRepo implements PaymentRepo
{
    /**
     * @inheritDoc
     */
    protected function model(): string
    {
        return Payment::class;
    }
}
