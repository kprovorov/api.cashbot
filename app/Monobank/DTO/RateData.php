<?php

namespace App\Monobank\DTO;

use Spatie\DataTransferObject\DataTransferObject;

class RateData extends DataTransferObject
{
    public int $currencyCodeA;
    public int $currencyCodeB;
    public int $date;
    public ?float $rateBuy;
    public ?float $rateSell;
    public ?float $rateCross;
}
