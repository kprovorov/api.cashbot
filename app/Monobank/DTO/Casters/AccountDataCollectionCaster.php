<?php

namespace App\Monobank\DTO\Casters;

use App\Monobank\DTO\Collections\AccountDataCollection;
use App\Monobank\DTO\AccountData;
use Spatie\DataTransferObject\Caster;

class AccountDataCollectionCaster implements Caster
{
    public function cast(mixed $value): AccountDataCollection
    {
        return new AccountDataCollection(
            array_map(
                fn(array $data) => new AccountData(...$data),
                $value
            )
        );
    }
}
