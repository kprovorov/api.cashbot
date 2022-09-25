<?php

namespace App\Monobank\DTO\Collections;

use App\Monobank\DTO\RateData;
use Illuminate\Support\Collection;

class RateDataCollection extends Collection
{
    public function offsetGet($key): RateData
    {
        return parent::offsetGet($key);
    }
}
