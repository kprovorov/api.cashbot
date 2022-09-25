<?php

namespace App\PaymentModule\Repositories;

use App\PaymentModule\Models\Payment;
use App\Support\Repositories\EloquentRepo;

/**
 * @extends EloquentRepo<Payment>
 */
class EloquentPaymentRepo extends EloquentRepo implements PaymentRepo
{
    /**
     * {@inheritDoc}
     */
    protected function model(): string
    {
        return Payment::class;
    }
}
