<?php

namespace App\Http\Integrations\Monobank\Requests;

use App\Monobank\DTO\ClientInfoResponseData;
use Illuminate\Support\Facades\Cache;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\CachePlugin\Contracts\Driver;
use Saloon\CachePlugin\Drivers\LaravelCacheDriver;
use Saloon\CachePlugin\Traits\HasCaching;
use Saloon\Contracts\Response;
use Saloon\Enums\Method;
use Saloon\Http\Request;

class ClientInfo extends Request implements Cacheable
{
    use HasCaching;

    /**
     * Define the HTTP method
     */
    protected Method $method = Method::GET;

    /**
     * Define the endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/personal/client-info';
    }

    public function resolveCacheDriver(): Driver
    {
        return new LaravelCacheDriver(Cache::store('redis'));
    }

    public function cacheExpiryInSeconds(): int
    {
        return 30;
    }

    public function createDtoFromResponse(Response $response): mixed
    {
        return ClientInfoResponseData::fromResponse($response);
    }
}
