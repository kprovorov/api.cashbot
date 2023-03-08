<?php

namespace App\PaymentModule\DTO;

use App\Enums\Currency;
use Spatie\DataTransferObject\DataTransferObject;

class UpdatePaymentGeneralData extends DataTransferObject
{
    public int $account_id;

    public int $amount;

    public Currency $currency;

    public string $description;
}
