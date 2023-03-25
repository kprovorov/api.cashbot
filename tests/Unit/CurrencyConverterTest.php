<?php

namespace Tests\Unit;

use App\Enums\Currency;
use App\Services\CurrencyConverter;
use Saloon\Http\Faking\MockResponse;
use Saloon\Laravel\Saloon;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;
use Tests\TestCase;

class CurrencyConverterTest extends TestCase
{
    /**
     * @test
     *
     * @throws UnknownProperties
     */
    public function it_successfully_converts_eur_to_uah_currency(): void
    {
        Saloon::fake([
            MockResponse::make([
                [
                    'currencyCodeA' => 840,
                    'currencyCodeB' => 980,
                    'date' => 1663794609,
                    'rateBuy' => 36.65,
                    'rateSell' => 37.9507,
                ],
                [
                    'currencyCodeA' => 978,
                    'currencyCodeB' => 980,
                    'date' => 1663861209,
                    'rateBuy' => 36.1,
                    'rateSell' => 37.9507,
                ],
                [
                    'currencyCodeA' => 978,
                    'currencyCodeB' => 840,
                    'date' => 1663861209,
                    'rateBuy' => 0.982,
                    'rateSell' => 1,
                ],
            ], 200),
        ]);

        $service = $this->app->make(CurrencyConverter::class);

        $res = $service->getRate(Currency::EUR, Currency::UAH);

        $this->assertEquals(0.0277, $res);
    }

    /**
     * @test
     *
     * @throws UnknownProperties
     */
    public function it_successfully_converts_uah_to_eur_currency(): void
    {
        Saloon::fake([
            MockResponse::make([
                [
                    'currencyCodeA' => 840,
                    'currencyCodeB' => 980,
                    'date' => 1663794609,
                    'rateBuy' => 36.65,
                    'rateSell' => 37.9507,
                ],
                [
                    'currencyCodeA' => 978,
                    'currencyCodeB' => 980,
                    'date' => 1663861209,
                    'rateBuy' => 36.1,
                    'rateSell' => 37.9507,
                ],
                [
                    'currencyCodeA' => 978,
                    'currencyCodeB' => 840,
                    'date' => 1663861209,
                    'rateBuy' => 0.982,
                    'rateSell' => 1,
                ],
            ], 200),
        ]);

        $service = $this->app->make(CurrencyConverter::class);

        $res = $service->getRate(Currency::UAH, Currency::EUR);

        $this->assertEquals(37.9507, $res);
    }
}
