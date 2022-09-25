<?php

namespace App\Monobank\DTO;

use Spatie\DataTransferObject\DataTransferObject;

class AccountData extends DataTransferObject
{
    public string $id;

    public string $sendId;

    public int $currencyCode;

    public ?string $cashbackType = null;

    public int $balance;

    public int $creditLimit;

    /**
     * @var string[]
     */
    public array $maskedPan;

    public string $type;

    public string $iban;
}
