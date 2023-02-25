<?php

namespace App\PaymentModule\DTO;

use App\Enums\Currency;
use Carbon\Carbon;
use Spatie\DataTransferObject\DataTransferObject;

class UpdatePaymentData extends DataTransferObject
{
    public int $account_id;

    public string $description;

    public int $amount;

    public Currency $currency;

    public Carbon $date;

    public ?Carbon $ends_on = null;

    public bool $hidden;

    public bool $auto_apply;
}
