<?php

namespace App\Http\Integrations\Monobank;

use App\Http\Integrations\Monobank\Requests\ClientInfo;
use App\Http\Integrations\Monobank\Requests\GetRates;
use Saloon\Http\Connector;
use Saloon\Http\Response;
use Saloon\Traits\Plugins\AcceptsJson;

class Monobank extends Connector
{
    use AcceptsJson;

    public function __construct(private readonly string $token, private string $baseUrl)
    {
    }

    /**
     * The Base URL of the API
     *
     * @return string
     */
    public function resolveBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * Default headers for every request
     *
     * @return string[]
     */
    protected function defaultHeaders(): array
    {
        return [
            'X-Token' => $this->token
        ];
    }

    /**
     * Default HTTP client options
     *
     * @return string[]
     */
    protected function defaultConfig(): array
    {
        return [];
    }

    /**
     * Get client info
     */
    public function getClientInfo(): Response
    {
        return $this->send(new ClientInfo());
    }

    public function getRates(): Response
    {
        return $this->send(new GetRates());
    }
}
