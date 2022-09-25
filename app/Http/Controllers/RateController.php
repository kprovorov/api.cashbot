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
     * @return array
     */
    public function __invoke(Request $request): array
    {
        $from = Currency::from(strtoupper((string) $request->get('from')));
        $to = Currency::from(strtoupper((string) $request->get('to')));

        try {
            return [
                'from' => $from->name,
                'to' => $to->name,
                'rate' => $this->currencyConverter->getRate($from, $to),
            ];
        } catch (\Exception) {
            return [];
        }
    }
}
