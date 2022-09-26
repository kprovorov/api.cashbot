<?php

namespace App\AccountModule\DTO;

use Spatie\DataTransferObject\DataTransferObject;

class UpdateAccountData extends DataTransferObject
{
    public string $name;

    public string $currency;

    public int $balance;
}
