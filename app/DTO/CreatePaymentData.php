<?php

namespace App\DTO;

use Spatie\DataTransferObject\DataTransferObject;

class CreatePaymentData extends DataTransferObject
{
    public int $jarId;
    public ?int $groupId;
    public string $description;
    public int $amount;
    public string $currency;
    public string $date;
}
