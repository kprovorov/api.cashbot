<?php

namespace App\DTO;

use App\Enums\Currency;
use Spatie\DataTransferObject\DataTransferObject;

class UpdatePaymentData extends DataTransferObject
{
    public int $jar_id;
    public string $description;
    public int $amount;
    public Currency $currency;
    public string $date;
    public bool $hidden;
}
