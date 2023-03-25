<?php

namespace App\Monobank\DTO;

use App\Monobank\DTO\Casters\AccountDataCollectionCaster;
use App\Monobank\DTO\Casters\JarDataCollectionCaster;
use App\Monobank\DTO\Collections\AccountDataCollection;
use App\Monobank\DTO\Collections\JarDataCollection;
use Saloon\Contracts\Response;
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
    public ?JarDataCollection $jars = null;

    public static function fromResponse(Response $response): self
    {
        $data = $response->json();

        return new static($data);
    }
}
