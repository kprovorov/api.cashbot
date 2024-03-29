<?php

namespace App\Services;

use App\Enums\Currency;
use App\Http\Integrations\Monobank\Monobank;
use App\Monobank\DTO\RateData;
use GuzzleHttp\Exception\GuzzleException;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class CurrencyConverter
{
    protected array $rates = [];

    public function __construct(protected readonly Monobank $monobank)
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
        if ($from === $to) {
            return 1;
        }

        return $this->getRates()[$from->name][$to->name];
    }

    /**
     * Get all currency exchange rates
     */
    public function getRates(): array
    {
        if (count($this->rates) === 0) {
            $this->fetchMonobankRates();
        }

        return $this->rates;
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
        $this->monobank
            ->getRates()
            ->dto()

            // Filter out unsupported currencies
            ->filter(fn (RateData $rate) => in_array($rate->currencyCodeA, Currency::getNumericCodes())
                && in_array($rate->currencyCodeB, Currency::getNumericCodes()))

            // Map to rates
            ->map(function (RateData $rate) {
                $currencyCodeA = Currency::fromNumeric($rate->currencyCodeA);
                $currencyCodeB = Currency::fromNumeric($rate->currencyCodeB);

                $this->rates[$currencyCodeA->name][$currencyCodeB->name] = round(1 / $rate->rateBuy, 4);
                $this->rates[$currencyCodeB->name][$currencyCodeA->name] = round($rate->rateSell, 4);
            });
    }

    /**
     * Convert amount from one currency to another
     *
     *
     * @throws GuzzleException
     * @throws UnknownProperties
     */
    public function convert(int $amount, Currency $currencyFrom, Currency $currencyTo): int
    {
        $rate = $amount > 0
            ? $this->getRate($currencyFrom, $currencyTo)
            : $this->getRate($currencyTo, $currencyFrom);

        return (int) round($amount > 0 ? $amount / $rate : $amount * $rate, 4);
    }
}
