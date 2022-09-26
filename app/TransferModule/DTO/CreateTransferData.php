<?php

namespace App\TransferModule\DTO;

use Spatie\DataTransferObject\DataTransferObject;

class CreateTransferData extends DataTransferObject
{
    public int $from_payment_id;

    public int $to_payment_id;
}
