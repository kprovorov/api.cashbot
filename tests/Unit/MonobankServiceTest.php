<?php

namespace Tests\Unit;

use App\Monobank\DTO\ClientInfoResponseData;
use App\Monobank\DTO\Collections\RateDataCollection;
use App\Monobank\Services\MonobankClient;
use App\Monobank\Services\MonobankService;
use Arr;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;
use Tests\TestCase;

class MonobankServiceTest extends TestCase
{
    /**
     * @test
     *
     * @return void
     *
     * @throws GuzzleException
     * @throws UnknownProperties
     */
    public function it_successfully_gets_rates(): void
    {
        $responseData = [
            [
                'currencyCodeA' => 840,
                'currencyCodeB' => 980,
                'date' => 1552392228,
                'rateSell' => 27,
                'rateBuy' => 27.2,
                'rateCross' => 27.1,
            ],
        ];

        $mock = new MockHandler([
            new Response(
                200, body: json_encode($responseData)
            ),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $this->app->instance(MonobankClient::class, new MonobankClient($client));

        $service = $this->app->make(MonobankService::class);

        $res = $service->getRates();

        $this->assertInstanceOf(RateDataCollection::class, $res);
        $this->assertEquals($responseData[0], $res->first()->toArray());
    }

    /**
     * @test
     *
     * @return void
     *
     * @throws GuzzleException
     * @throws UnknownProperties
     */
    public function it_successfully_gets_client_info(): void
    {
        $responseData = [
            'clientId' => '3MSaMMtczs',
            'name' => 'Мазепа Іван',
            'webHookUrl' => 'https://example.com/some_random_data_for_security',
            'permissions' => 'psfj',
            'accounts' => [
                [
                    'id' => 'kKGVoZuHWzqVoZuH',
                    'sendId' => 'uHWzqVoZuH',
                    'balance' => 10000000,
                    'creditLimit' => 10000000,
                    'type' => 'black',
                    'currencyCode' => 980,
                    'cashbackType' => 'UAH',
                    'maskedPan' => [
                        '537541******1234',
                    ],
                    'iban' => 'UA733220010000026201234567890',
                ],
            ],
            'jars' => [
                [
                    'id' => 'kKGVoZuHWzqVoZuH',
                    'sendId' => 'uHWzqVoZuH',
                    'title' => 'На тепловізор',
                    'description' => 'На тепловізор',
                    'currencyCode' => 980,
                    'balance' => 1000000,
                    'goal' => 10000000,
                ],
            ],
        ];

        $mock = new MockHandler([
            new Response(
                200, body: json_encode($responseData)
            ),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $this->app->instance(MonobankClient::class, new MonobankClient($client));

        $service = $this->app->make(MonobankService::class);

        $res = $service->getClientInfo();

        $this->assertInstanceOf(ClientInfoResponseData::class, $res);
        $this->assertEquals(
            Arr::except($responseData, ['accounts', 'jars']),
            $res->except('accounts', 'jars')->toArray()
        );
        $this->assertEquals(
            $responseData['accounts'][0],
            $res->accounts->first()->toArray()
        );
        $this->assertEquals(
            $responseData['jars'][0],
            $res->jars->first()->toArray()
        );
    }
}
