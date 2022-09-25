<?php

namespace App\Monobank\DTO;

use Spatie\DataTransferObject\DataTransferObject;

class JarData extends DataTransferObject
{
    public string $id;
    public string $sendId;
    public string $title;
    public string $description;
    public int $currencyCode;
    public int $balance;
    public int $goal;
}
