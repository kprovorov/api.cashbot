<?php

namespace App\Monobank\Services;

use App\Monobank\DTO\ClientInfoResponseData;
use App\Monobank\DTO\Collections\RateDataCollection;
use App\Monobank\DTO\RateData;
use GuzzleHttp\Exception\GuzzleException;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class MonobankService
{
    public function __construct(protected readonly MonobankClient $client)
    {
    }

    /**
     * Get currency exchange rates
     *
     * @return RateDataCollection
     *
     * @throws GuzzleException
     * @throws UnknownProperties
     */
    public function getRates(): RateDataCollection
    {
        return new RateDataCollection(
            array_map(function (array $rate) {
                return new RateData($rate);
            }, $this->client->getRates())
        );
    }

    /**
     * Get Client Info
     *
     * @return ClientInfoResponseData
     *
     * @throws GuzzleException
     * @throws UnknownProperties
     */
    public function getClientInfo(): ClientInfoResponseData
    {
        return new ClientInfoResponseData($this->client->getClientInfo());
    }
}
