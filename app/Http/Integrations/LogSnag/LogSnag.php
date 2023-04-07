<?php

namespace App\Http\Integrations\LogSnag;

use App\Http\Integrations\LogSnag\Requests\Log;
use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;
use Saloon\Http\Response;

class LogSnag extends Connector
{
    use AcceptsJson;

    public function __construct(string $token, private readonly string $baseUrl, private readonly string $project)
    {
        $this->withTokenAuth($token);
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
        return [];
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

    public function log(array $body): Response
    {
        $request = new Log();
        $request->body()->merge([
            'project' => $this->project,
            ...$body
        ]);

        return $this->send($request);
    }
}
