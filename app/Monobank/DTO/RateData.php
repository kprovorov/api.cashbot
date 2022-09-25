<?php

namespace App\Monobank\DTO;

use Spatie\DataTransferObject\DataTransferObject;

class RateData extends DataTransferObject
{
    public int $currencyCodeA;

    public int $currencyCodeB;

    public int $date;

    public ?float $rateBuy = null;

    public ?float $rateSell = null;

    public ?float $rateCross = null;
}
