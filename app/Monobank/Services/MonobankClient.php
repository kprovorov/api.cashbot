<?php

namespace App\Monobank\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

class MonobankClient
{
    public function __construct(protected readonly Client $client)
    {
    }

    protected function parseResponse(ResponseInterface $res): array
    {
        return json_decode($res->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * Get currency exchange rates
     *
     *
     * @throws GuzzleException
     */
    public function getRates(): array
    {
        $res = $this->client->get('bank/currency');

        return $this->parseResponse($res);
    }

    /**
     * Get customer info
     *
     *
     * @throws GuzzleException
     */
    public function getClientInfo(): array
    {
        $res = $this->client->get('personal/client-info');

        return $this->parseResponse($res);
    }
}
