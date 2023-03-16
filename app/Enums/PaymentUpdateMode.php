<?php

namespace App\Enums;

enum PaymentUpdateMode: string
{
    case SINGLE = 'SINGLE';
    case FUTURE = 'FUTURE';
    case ALL = 'ALL';
}
