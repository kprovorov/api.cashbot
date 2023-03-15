<?php

namespace App\Enums;

enum PaymentUpdateMode: string
{
    case SINGLE = 'single';
    case FUTURE = 'future';
    case ALL = 'all';
}
