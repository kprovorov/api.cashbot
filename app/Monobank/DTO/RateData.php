<?php

namespace App\Monobank\DTO;

use Saloon\Contracts\Response;
use Spatie\DataTransferObject\DataTransferObject;

class RateData extends DataTransferObject
{
    public int $currencyCodeA;

    public int $currencyCodeB;

    public int $date;

    public ?float $rateBuy = null;

    public ?float $rateSell = null;

    public ?float $rateCross = null;

    public static function fromResponse(Response $response): self
    {
        $data = $response->json();

        return new static($data);
    }
}
