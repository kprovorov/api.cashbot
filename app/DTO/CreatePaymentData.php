<?php

namespace App\DTO;

use App\Enums\Currency;
use Carbon\Carbon;
use Spatie\DataTransferObject\DataTransferObject;

class CreatePaymentData extends DataTransferObject
{
    public int $jar_id;

    public ?int $group_id;

    public string $description;

    public int $amount;

    public Currency $currency;

    public Carbon $date;

    public ?Carbon $ends_on;
}
