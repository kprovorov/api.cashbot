<?php

namespace App\DTO;

use App\Enums\Currency;
use Carbon\Carbon;
use Spatie\DataTransferObject\DataTransferObject;

class CurrencyRateData extends DataTransferObject
{
    public Currency $currencyCodeA;
    public Currency $currencyCodeB;
    public Carbon $date;
    public float $rateBuy;
    public float $rateSell;
}
