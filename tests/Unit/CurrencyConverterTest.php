<?php

namespace Tests\Unit;

use App\Enums\Currency;
use App\Services\CurrencyConverter;
use App\Services\MonobankApi;
use Tests\TestCase;

class CurrencyConverterTest extends TestCase
{
    /**
     * @test
     * @return void
     */
    public function it_successfully_converts_EUR_to_UAH_currency(): void
    {
        $mock = $this->mock(MonobankApi::class);
        $mock->shouldReceive('getRates')->once()->andReturn([
            [
                "currencyCodeA" => 840,
                "currencyCodeB" => 980,
                "date"          => 1663794609,
                "rateBuy"       => 36.65,
                "rateSell"      => 37.9507,
            ],
            [
                "currencyCodeA" => 978,
                "currencyCodeB" => 980,
                "date"          => 1663861209,
                "rateBuy"       => 36.1,
                "rateSell"      => 37.9507,
            ],
            [
                "currencyCodeA" => 978,
                "currencyCodeB" => 840,
                "date"          => 1663861209,
                "rateBuy"       => 0.982,
                "rateSell"      => 1,
            ],
        ]);
        $service = $this->app->make(CurrencyConverter::class);


        $res = $service->getRate(Currency::EUR, Currency::UAH);

        $this->assertEquals(36.1, $res);
    }

    /**
     * @test
     * @return void
     */
    public function it_successfully_converts_UAH_to_EUR_currency(): void
    {
        $mock = $this->mock(MonobankApi::class);
        $mock->shouldReceive('getRates')->once()->andReturn([
            [
                "currencyCodeA" => 840,
                "currencyCodeB" => 980,
                "date"          => 1663794609,
                "rateBuy"       => 36.65,
                "rateSell"      => 37.9507,
            ],
            [
                "currencyCodeA" => 978,
                "currencyCodeB" => 980,
                "date"          => 1663861209,
                "rateBuy"       => 36.1,
                "rateSell"      => 37.9507,
            ],
            [
                "currencyCodeA" => 978,
                "currencyCodeB" => 840,
                "date"          => 1663861209,
                "rateBuy"       => 0.982,
                "rateSell"      => 1,
            ],
        ]);
        $service = $this->app->make(CurrencyConverter::class);


        $res = $service->getRate(Currency::UAH, Currency::EUR);

        $this->assertEquals(0.0263, $res);
    }
}
