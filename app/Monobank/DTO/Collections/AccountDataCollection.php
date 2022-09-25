<?php

namespace App\Monobank\DTO\Collections;

use App\Monobank\DTO\AccountData;
use Illuminate\Support\Collection;

class AccountDataCollection extends Collection
{
    public function offsetGet($key): AccountData
    {
        return parent::offsetGet($key);
    }
}
