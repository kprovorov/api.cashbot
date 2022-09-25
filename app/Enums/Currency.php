<?php

namespace App\Enums;

enum Currency: string
{
    case EUR = 'EUR';
    case USD = 'USD';
    case UAH = 'UAH';

    /**
     * @return int[]
     */
    protected static function getCodes(): array
    {
        return [
            self::EUR->name => 978,
            self::USD->name => 840,
            self::UAH->name => 980,
        ];
    }

    /**
     * Get ISO4217 alphabetical code from numeric
     */
    public static function fromNumeric(int $code): static
    {
        $name = array_search($code, self::getCodes());

        return self::from($name);

//        return match ($code) {
//            978 => self::EUR,
//            840 => self::USD,
//            980 => self::UAH,
//            default => null
//        };
    }

    /**
     * @return int[]
     */
    public static function getNumericCodes(): array
    {
        return array_values(self::getCodes());
    }
}
