<?php

namespace Tests\Unit;

use App\Enums\Currency;
use App\Monobank\DTO\Collections\RateDataCollection;
use App\Monobank\DTO\RateData;
use App\Monobank\Services\MonobankService;
use App\Services\CurrencyConverter;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;
use Tests\TestCase;

class CurrencyConverterTest extends TestCase
{
    /**
     * @test
     * @return void
     * @throws UnknownProperties
     */
    public function it_successfully_converts_eur_to_uah_currency(): void
    {
        $mock = $this->mock(MonobankService::class);
        $mock->shouldReceive('getRates')->once()->andReturn(
            new RateDataCollection([
                new RateData([
                    "currencyCodeA" => 840,
                    "currencyCodeB" => 980,
                    "date"          => 1663794609,
                    "rateBuy"       => 36.65,
                    "rateSell"      => 37.9507,
                ]),
                new RateData([
                    "currencyCodeA" => 978,
                    "currencyCodeB" => 980,
                    "date"          => 1663861209,
                    "rateBuy"       => 36.1,
                    "rateSell"      => 37.9507,
                ]),
                new RateData([
                    "currencyCodeA" => 978,
                    "currencyCodeB" => 840,
                    "date"          => 1663861209,
                    "rateBuy"       => 0.982,
                    "rateSell"      => 1,
                ]),
            ])
        );
        $service = $this->app->make(CurrencyConverter::class);


        $res = $service->getRate(Currency::EUR, Currency::UAH);

        $this->assertEquals(36.1, $res);
    }

    /**
     * @test
     * @return void
     * @throws UnknownProperties
     */
    public function it_successfully_converts_uah_to_eur_currency(): void
    {
        $mock = $this->mock(MonobankService::class);
        $mock->shouldReceive('getRates')->once()->andReturn(
            new RateDataCollection([
                new RateData([
                    "currencyCodeA" => 840,
                    "currencyCodeB" => 980,
                    "date"          => 1663794609,
                    "rateBuy"       => 36.65,
                    "rateSell"      => 37.9507,
                ]),
                new RateData([
                    "currencyCodeA" => 978,
                    "currencyCodeB" => 980,
                    "date"          => 1663861209,
                    "rateBuy"       => 36.1,
                    "rateSell"      => 37.9507,
                ]),
                new RateData([
                    "currencyCodeA" => 978,
                    "currencyCodeB" => 840,
                    "date"          => 1663861209,
                    "rateBuy"       => 0.982,
                    "rateSell"      => 1,
                ]),
            ])
        );
        $service = $this->app->make(CurrencyConverter::class);


        $res = $service->getRate(Currency::UAH, Currency::EUR);

        $this->assertEquals(0.0263, $res);
    }
}
