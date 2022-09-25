<?php

namespace App\Monobank\DTO;

use App\Monobank\DTO\Casters\AccountDataCollectionCaster;
use App\Monobank\DTO\Casters\JarDataCollectionCaster;
use App\Monobank\DTO\Collections\AccountDataCollection;
use App\Monobank\DTO\Collections\JarDataCollection;
use Spatie\DataTransferObject\Attributes\CastWith;
use Spatie\DataTransferObject\DataTransferObject;

class ClientInfoResponseData extends DataTransferObject
{
    public string $clientId;

    public string $name;

    public string $webHookUrl;

    public string $permissions;

    #[CastWith(AccountDataCollectionCaster::class)]
    public AccountDataCollection $accounts;

    #[CastWith(JarDataCollectionCaster::class)]
    public ?JarDataCollection $jars;
}
