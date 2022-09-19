<?php

namespace App\Services;

use App\DTO\CurrencyRateData;
use App\Enums\Currency;
use Cache;
use Carbon\Carbon;
use Exception;
use Http;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

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
        $rates = array_filter($this->getRates(), function (CurrencyRateData $rate) use ($to, $from) {
            return ($rate->currencyCodeA->name === $from
                    && $rate->currencyCodeB->name === $to)
                || ($rate->currencyCodeA->name === $to
                    && $rate->currencyCodeB->name === $from);
        });

        $this->setRates($rates);

        return isset($this->rates[$from])
            ? $this->rates[$from][$to]
            : (isset($this->rates[$to]) ? [
                'buy'  => round(1 / $this->rates[$to][$from]['sell'], 4),
                'sell' => round(1 / $this->rates[$to][$from]['buy'], 4),
            ] : throw new Exception('No such currency'));
    }

    /**
     * @return CurrencyRateData[]
     * @throws UnknownProperties
     */
    protected function getRates(): array
    {
        $rates = Cache::get('rates') ?? $this->fetchRates();

        return array_map(function (array $rate) {
            return new CurrencyRateData([
                ...$rate,
                'date'          => Carbon::createFromTimestamp($rate['date']),
                'currencyCodeA' => Currency::fromNumeric($rate['currencyCodeA']),
                'currencyCodeB' => Currency::fromNumeric($rate['currencyCodeB']),
            ]);
        },
            array_filter($rates, function (array $rate) {
                return in_array($rate['currencyCodeA'], Currency::getNumericCodes())
                    && in_array($rate['currencyCodeB'], Currency::getNumericCodes());
            }));
    }

    /**
     * Fetch rates from the API
     *
     * @return array
     */
    protected function fetchRates(): array
    {
        $rates = Http::get('https://api.monobank.ua/bank/currency')->json();

        Cache::put('rates', $rates, 60 * 5);

        return $rates;
    }
}
