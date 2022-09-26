<?php

namespace App\UserModule\DTO;

use Spatie\DataTransferObject\DataTransferObject;

class CreateUserData extends DataTransferObject
{
    public string $name;

    public string $email;

    public string $password;
}
