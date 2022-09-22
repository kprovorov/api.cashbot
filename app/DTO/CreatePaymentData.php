<?php

namespace App\DTO;

use App\Enums\Currency;
use Spatie\DataTransferObject\DataTransferObject;

class CreatePaymentData extends DataTransferObject
{
    public int $jarId;
    public ?int $groupId;
    public string $description;
    public int $amount;
    public Currency $currency;
    public string $date;
}
