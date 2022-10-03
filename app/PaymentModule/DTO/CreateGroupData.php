<?php

namespace App\PaymentModule\DTO;

use Spatie\DataTransferObject\DataTransferObject;

class CreateGroupData extends DataTransferObject
{
    public string $name;
}
