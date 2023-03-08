<?php

namespace App\Enums;

enum RepeatUnit: string
{
    case NONE = 'none';
    case DAY = 'day';
    case WEEK = 'week';
    case MONTH = 'month';
    case QUARTER = 'quarter';
    case YEAR = 'year';
}
