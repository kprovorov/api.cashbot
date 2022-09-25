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
            ->filter(fn(RateData $rate) => in_array($rate->currencyCodeA, Currency::getNumericCodes())
                && in_array($rate->currencyCodeB, Currency::getNumericCodes()))

            // Map to rates
            ->map(function (RateData $rate) {
                $currencyCodeA = Currency::fromNumeric($rate->currencyCodeA);
                $currencyCodeB = Currency::fromNumeric($rate->currencyCodeB);

                $this->rates[$currencyCodeA->name][$currencyCodeB->name] = round($rate->rateBuy, 4);
                $this->rates[$currencyCodeB->name][$currencyCodeA->name] = round(1 / $rate->rateSell, 4);
            });
    }
}
