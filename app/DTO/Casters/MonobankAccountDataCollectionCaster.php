<?php

namespace App\DTO\Casters;

use App\DTO\Collections\MonobankAccountDataCollection;
use App\DTO\MonobankAccountData;
use Spatie\DataTransferObject\Caster;

class MonobankAccountDataCollectionCaster implements Caster
{
    public function cast(mixed $value): MonobankAccountDataCollection
    {
        return new MonobankAccountDataCollection(
            array_map(
                fn(array $data) => new MonobankAccountData(...$data),
                $value
            )
        );
    }
}
