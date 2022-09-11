<?php

namespace App\DTO\Collections;

use App\DTO\MonobankAccountData;
use Illuminate\Support\Collection;

class MonobankAccountDataCollection extends Collection
{
    public function offsetGet($key): MonobankAccountData
    {
        return parent::offsetGet($key);
    }
}
