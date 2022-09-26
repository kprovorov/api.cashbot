<?php

namespace App\UserModule\DTO;

use Spatie\DataTransferObject\DataTransferObject;

class UpdateUserData extends DataTransferObject
{
    public string $name;

    public string $email;

    public ?string $email_verified_at;

    public string $password;

    public ?string $two_factor_secret;

    public ?string $two_factor_recovery_codes;

    public ?string $remember_token;
}
