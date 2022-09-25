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

    /**
     * @throws GuzzleException
     * @throws UnknownProperties
     */
    public function __construct(protected readonly MonobankService $monobankService)
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
     * @throws GuzzleException
     * @throws UnknownProperties
     */
    protected function fetchMonobankRates(): void
    {
        $this->monobankService
            ->getRates()

            // Filter out unsupported currencies
            ->filter(function (RateData $rate) {
                return in_array($rate->currencyCodeA, Currency::getNumericCodes())
                    && in_array($rate->currencyCodeB, Currency::getNumericCodes());
            })

            // Map to rates
            ->map(function (RateData $rate) {
                $currencyCodeA = Currency::fromNumeric($rate->currencyCodeA);
                $currencyCodeB = Currency::fromNumeric($rate->currencyCodeB);

                $this->rates[$currencyCodeA->name][$currencyCodeB->name] = round($rate->rateBuy, 4);
                $this->rates[$currencyCodeB->name][$currencyCodeA->name] = round(1 / $rate->rateSell, 4);
            });
    }
}
