<?php

namespace App\Monobank\DTO\Collections;

use App\Monobank\DTO\JarData;
use Illuminate\Support\Collection;

class JarDataCollection extends Collection
{
    public function offsetGet($key): JarData
    {
        return parent::offsetGet($key);
    }
}
