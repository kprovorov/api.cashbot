<?php

namespace App\Services;

use App\DTO\CurrencyRateData;
use Exception;

class CurrencyConverter
{
    protected array $rates = [];

    /**
     * @param CurrencyRateData[] $rates
     * @return void
     */
    public function setRates(array $rates): void
    {
        array_map(function (CurrencyRateData $rate) {
            $this->rates[$rate->currencyCodeA->name][$rate->currencyCodeB->name] = [
                'buy'  => round($rate->rateBuy, 4),
                'sell' => round($rate->rateSell, 4),
            ];
        }, $rates);
    }

    /**
     * @param string $from
     * @param string $to
     * @return array
     * @throws Exception
     */
    public function getRate(string $from, string $to): array
    {
        return isset($this->rates[$from])
            ? $this->rates[$from][$to]
            : (isset($this->rates[$to]) ? [
                'buy'  => round(1 / $this->rates[$to][$from]['sell'], 4),
                'sell' => round(1 / $this->rates[$to][$from]['buy'], 4),
            ] : throw new Exception('No such currency'));
    }
}
