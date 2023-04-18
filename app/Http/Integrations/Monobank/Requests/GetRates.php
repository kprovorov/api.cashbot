<?php

namespace App\Http\Integrations\Monobank\Requests;

use App\Monobank\DTO\Collections\RateDataCollection;
use Illuminate\Support\Facades\Cache;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\CachePlugin\Contracts\Driver;
use Saloon\CachePlugin\Drivers\LaravelCacheDriver;
use Saloon\CachePlugin\Traits\HasCaching;
use Saloon\Contracts\Response;
use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetRates extends Request implements Cacheable
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
        return '/bank/currency';
    }

    public function resolveCacheDriver(): Driver
    {
        return new LaravelCacheDriver(Cache::store('redis'));
    }

    public function cacheExpiryInSeconds(): int
    {
        return 300; // 5 minutes
    }

    public function createDtoFromResponse(Response $response): mixed
    {
        return RateDataCollection::fromResponse($response);
    }
}
