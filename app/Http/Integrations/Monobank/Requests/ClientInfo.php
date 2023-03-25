<?php

namespace App\Http\Integrations\Monobank\Requests;

use App\Monobank\DTO\ClientInfoResponseData;
use Illuminate\Support\Facades\Cache;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\CachePlugin\Drivers\LaravelCacheDriver;
use Saloon\CachePlugin\Traits\HasCaching;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Contracts\Response;
use Saloon\CachePlugin\Contracts\Driver;

class ClientInfo extends Request implements Cacheable
{
    use HasCaching;

    /**
     * Define the HTTP method
     *
     * @var Method
     */
    protected Method $method = Method::GET;

    /**
     * Define the endpoint for the request
     *
     * @return string
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
