<?php

namespace App\Http\Controllers;

use App\DTO\CurrencyRateData;
use App\Enums\Currency;
use App\Services\CurrencyConverter;
use Cache;
use Carbon\Carbon;
use Http;
use Illuminate\Http\Request;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class RateController extends Controller
{
    /**
     * @param Request $request
     * @return array
     * @throws UnknownProperties
     */
    public function __invoke(Request $request)
    {
        $from = strtoupper($request->get('from'));
        $to = strtoupper($request->get('to'));

        if ($from === $to) {
            return [
                'buy'  => 1,
                'sell' => 1,
            ];
        }

        $rates = $this->getRates();

        $r = array_filter($rates, function (CurrencyRateData $rate) use ($to, $from, $request) {
            return ($rate->currencyCodeA->name === $from
                    && $rate->currencyCodeB->name === $to)
                || ($rate->currencyCodeA->name === $to
                    && $rate->currencyCodeB->name === $from);
        });

        $currencyConverter = new CurrencyConverter();
        $currencyConverter->setRates($r);

        try {
            return $currencyConverter->getRate($from, $to);
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * @return array<CurrencyRateData>
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

    protected function fetchRates(): array
    {
        $rates = Http::get('https://api.monobank.ua/bank/currency')->json();

        Cache::put('rates', $rates, 60 * 5);

        return $rates;
    }
}
