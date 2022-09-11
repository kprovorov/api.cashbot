<?php

namespace App\DTO;

use App\DTO\Casters\MonobankAccountDataCollectionCaster;
use App\DTO\Collections\MonobankAccountDataCollection;
use Spatie\DataTransferObject\Attributes\CastWith;
use Spatie\DataTransferObject\DataTransferObject;

class MonobankClientInfoData extends DataTransferObject
{
    public string $clientId;
    public string $name;
    public string $webHookUrl;
    public string $permissions;

    #[CastWith(MonobankAccountDataCollectionCaster::class)]
    public MonobankAccountDataCollection $accounts;
}
