<?php

namespace App\AccountModule\DTO;

use App\Enums\Currency;
use Spatie\DataTransferObject\DataTransferObject;

class UpdateAccountData extends DataTransferObject
{
    public ?int $parent_id;

    public string $name;

    public Currency $currency;

    public int $balance;

    public ?string $provider_id;

    public ?string $provider;
}
