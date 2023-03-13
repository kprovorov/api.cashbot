<?php

namespace App\PaymentModule\DTO;

use App\Enums\Currency;
use App\Enums\RepeatUnit;
use Carbon\Carbon;
use Spatie\DataTransferObject\DataTransferObject;

class CreatePaymentData extends DataTransferObject
{
    public ?int $account_to_id;

    public ?int $account_from_id;

    public string $description;

    public int $amount;

    public Currency $currency;

    public Carbon $date;

    public ?Carbon $ends_on = null;

    public ?string $group;

    public bool $auto_apply;

    public RepeatUnit $repeat_unit;

    public int $repeat_interval;

    public ?Carbon $repeat_ends_on = null;
}
