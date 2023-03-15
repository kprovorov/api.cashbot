<?php

namespace App\AccountModule\DTO;

use Spatie\DataTransferObject\DataTransferObject;

class CreateAccountData extends DataTransferObject
{
    public int $user_id;

    public string $name;

    public string $currency;

    public int $balance;

    public ?string $provider_id;

    public ?string $provider;
}
