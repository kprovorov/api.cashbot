<?php

namespace App\Services;

use App\Enums\Currency;

class CurrencyConverter
{
    protected array $rates = [];

    public function __construct(protected readonly MonobankApi $monobankApi)
    {
        $this->fetchMonobankRates();
    }

    /**
     * Get currency exchange rate
     *
     * @param Currency $from
     * @param Currency $to
     * @return float
     */
    public function getRate(Currency $from, Currency $to): float
    {
        if ($from === $to) {
            return 1;
        }

        return $this->rates[$from->name][$to->name];
    }

    /**
     * Fetch Monobank exchange rates
     *
     * @return void
     */
    protected function fetchMonobankRates(): void
    {
        $rawMonobankRates = $this->monobankApi->getRates();

        // Filter unsupported currencies
        $filtered = array_filter($rawMonobankRates, function (array $rate) {
            return in_array($rate['currencyCodeA'], Currency::getNumericCodes())
                && in_array($rate['currencyCodeB'], Currency::getNumericCodes());
        });

        array_map(function (array $ratePair) {
            $currencyCodeA = Currency::fromNumeric($ratePair['currencyCodeA']);
            $currencyCodeB = Currency::fromNumeric($ratePair['currencyCodeB']);

            $this->rates[$currencyCodeA->name][$currencyCodeB->name] = round($ratePair['rateBuy'], 4);
            $this->rates[$currencyCodeB->name][$currencyCodeA->name] = round(1 / $ratePair['rateSell'], 4);
        }, $filtered);
    }
}
