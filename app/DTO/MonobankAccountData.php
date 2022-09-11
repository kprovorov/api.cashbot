<?php

namespace App\DTO;

use Spatie\DataTransferObject\DataTransferObject;

class MonobankAccountData extends DataTransferObject
{
    public string $id;
    public string $sendId;
    public int $currencyCode;
    public ?string $cashbackType;
    public int $balance;
    public int $creditLimit;
    /**
     * @var string[]
     */
    public array $maskedPan;
    public string $type;
    public string $iban;
}
