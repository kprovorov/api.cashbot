<?php

namespace App\Http\Controllers;

use App\Services\CurrencyConverter;

class RatesController extends Controller
{
    public function __construct(protected readonly CurrencyConverter $currencyConverter)
    {
    }

    public function __invoke(): array
    {
        return $this->currencyConverter->getRates();
    }
}
