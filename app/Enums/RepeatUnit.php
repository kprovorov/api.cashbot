<?php

namespace App\Enums;

enum RepeatUnit: string
{
    case NONE = 'NONE';
    case DAY = 'DAY';
    case WEEK = 'WEEK';
    case MONTH = 'MONTH';
    case QUARTER = 'QUARTER';
    case YEAR = 'YEAR';
}
