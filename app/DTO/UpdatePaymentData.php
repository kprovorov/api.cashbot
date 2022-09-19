<?php

namespace App\DTO;

use Spatie\DataTransferObject\DataTransferObject;

class UpdatePaymentData extends DataTransferObject
{
    public int $jarId;
    public string $description;
    public int $amount;
    public string $currency;
    public string $date;
}
