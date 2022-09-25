<?php

namespace App\Monobank\Services;

use Cache;
use Exception;
use GuzzleHttp\Client;
use malkusch\lock\mutex\Mutex;
use Str;

class MonobankClientCacheDecorator extends MonobankClient
{
    const TTL = 60; // 60 minutes

    public function __construct(protected readonly Mutex $mutex, Client $client)
    {
        parent::__construct($client);
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function getClientInfo(): array
    {
        return $this->getFromCacheOrFetch(__FUNCTION__, 5);
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function getRates(): array
    {
        return $this->getFromCacheOrFetch(__FUNCTION__);
    }

    /**
     * @param string $method
     * @param int|null $ttl
     * @return mixed
     * @throws Exception
     */
    protected function getFromCacheOrFetch(string $method, ?int $ttl = null): mixed
    {
        $cacheKey = Str::slug(Str::snake($method));

        return $this->mutex->synchronized(function () use ($cacheKey, $method, $ttl) {
            $cached = Cache::get($cacheKey);

            if ($cached) {
                return unserialize($cached);
            } else {
                $res = parent::$method();
                Cache::put($cacheKey, serialize($res), now()->addMinutes($ttl ?? self::TTL));

                return $res;
            }
        });
    }
}
