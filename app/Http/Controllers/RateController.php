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
    public function __construct(protected readonly CurrencyConverter $currencyConverter)
    {
    }


    /**
     * @param Request $request
     * @return array
     */
    public function __invoke(Request $request): array
    {
        $from = strtoupper($request->get('from'));
        $to = strtoupper($request->get('to'));

        if ($from === $to) {
            return [
                'buy'  => 1,
                'sell' => 1,
            ];
        }

        try {
            return $this->currencyConverter->getRate($from, $to);
        } catch (\Exception $e) {
            return [];
        }
    }


}
