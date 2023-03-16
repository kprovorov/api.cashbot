<?php

namespace App\Enums;

enum PaymentUpdateMode: string
{
    CASE SINGLE = 'SINGLE';
    CASE FUTURE = 'FUTURE';
    CASE ALL = 'ALL';
}
