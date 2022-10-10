<?php

namespace App\Services;

use App\Enums\Currency;
use App\Monobank\DTO\RateData;
use App\Monobank\Services\MonobankService;
use GuzzleHttp\Exception\GuzzleException;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class CurrencyConverter
{
    protected array $rates = [];

    public function __construct(protected readonly MonobankService $monobankService)
    {
    }

    /**
     * Get currency exchange rate
     *
     *
     * @throws GuzzleException
     * @throws UnknownProperties
     */
    public function getRate(Currency $from, Currency $to): float
    {
        if (count($this->rates) === 0) {
            $this->fetchMonobankRates();
        }

        if ($from === $to) {
            return 1;
        }

        return $this->rates[$from->name][$to->name];
    }

    /**
     * Fetch Monobank exchange rates
     *
     *
     * @throws GuzzleException
     * @throws UnknownProperties
     */
    protected function fetchMonobankRates(): void
    {
        $this->monobankService
            ->getRates()

            // Filter out unsupported currencies
            ->filter(fn (RateData $rate) => in_array($rate->currencyCodeA, Currency::getNumericCodes())
                && in_array($rate->currencyCodeB, Currency::getNumericCodes()))

            // Map to rates
            ->map(function (RateData $rate) {
                $currencyCodeA = Currency::fromNumeric($rate->currencyCodeA);
                $currencyCodeB = Currency::fromNumeric($rate->currencyCodeB);

                $this->rates[$currencyCodeA->name][$currencyCodeB->name] = round($rate->rateBuy, 6);
                $this->rates[$currencyCodeB->name][$currencyCodeA->name] = round(1 / $rate->rateSell, 6);
            });
    }

    /**
     * Convert amount from one currency to another
     *
     * @param  int  $amount
     * @param  Currency  $currencyFrom
     * @param  Currency  $currencyTo
     * @return int
     *
     * @throws GuzzleException
     * @throws UnknownProperties
     */
    public function convert(int $amount, Currency $currencyFrom, Currency $currencyTo): int
    {
        $rate = $amount < 0
            ? $this->getRate($currencyFrom, $currencyTo)
            : $this->getRate($currencyTo, $currencyFrom);

        return (int) round($amount < 0 ? $amount / $rate : $amount * $rate, 6);
    }
}
