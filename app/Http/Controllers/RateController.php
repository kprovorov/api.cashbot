<?php

namespace App\Http\Controllers;

use App\Enums\Currency;
use App\Services\CurrencyConverter;
use Illuminate\Http\Request;

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
        $from = Currency::from(strtoupper($request->get('from')));
        $to = Currency::from(strtoupper($request->get('to')));

        try {
            return [
                'from' => $from->name,
                'to'   => $to->name,
                'rate' => $this->currencyConverter->getRate($from, $to),
            ];
        } catch (\Exception $e) {
            return [];
        }
    }


}
