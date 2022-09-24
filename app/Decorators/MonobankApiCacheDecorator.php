<?php

namespace App\Decorators;

use App\Services\MonobankApi;
use Cache;
use Exception;
use malkusch\lock\mutex\Mutex;

class MonobankApiCacheDecorator extends MonobankApi
{
    const TTL = 60; // 60 minutes

    public function __construct(protected readonly Mutex $mutex)
    {
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function getRawClientInfo(): array
    {
        return $this->getCached(__FUNCTION__, 5);
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function getRates(): array
    {
        return $this->getCached(__FUNCTION__);
    }

    /**
     * @param string $method
     * @param int|null $ttl
     * @return mixed
     * @throws Exception
     */
    protected function getCached(string $method, ?int $ttl = null): mixed
    {
        return $this->mutex->synchronized(function () use ($method, $ttl) {
            $cached = Cache::get($method);

            if ($cached) {
                return unserialize($cached);
            } else {
                $res = parent::$method();
                Cache::put($method, serialize($res), now()->addMinutes($ttl ?? self::TTL));

                return $res;
            }
        });
    }
}
