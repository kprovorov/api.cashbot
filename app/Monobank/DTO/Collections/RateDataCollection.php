<?php

namespace App\Monobank\DTO\Collections;

use App\Monobank\DTO\RateData;
use Illuminate\Support\Collection;
use Saloon\Contracts\Response;

class RateDataCollection extends Collection
{
    public function offsetGet($key): RateData
    {
        return parent::offsetGet($key);
    }

    public static function fromResponse(Response $response): self
    {
        return new static(
            array_map(fn (array $rate) => new RateData($rate), $response->json())
        );
    }
}
