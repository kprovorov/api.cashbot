<?php

namespace App\AccountModule\DTO;

use Spatie\DataTransferObject\DataTransferObject;

class UpdateJarData extends DataTransferObject
{
    public int $account_id;
    public string $name;
    public bool $default;
}
