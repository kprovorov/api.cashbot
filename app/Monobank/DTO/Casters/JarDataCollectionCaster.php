<?php

namespace App\Monobank\DTO\Casters;

use App\Monobank\DTO\Collections\JarDataCollection;
use App\Monobank\DTO\JarData;
use Spatie\DataTransferObject\Caster;

class JarDataCollectionCaster implements Caster
{
    public function cast(mixed $value): JarDataCollection
    {
        return new JarDataCollection(
            array_map(
                fn (array $data) => new JarData(...$data),
                $value
            )
        );
    }
}
