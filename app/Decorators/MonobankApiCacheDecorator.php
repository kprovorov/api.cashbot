<?php

namespace App\Decorators;

use App\Services\MonobankApi;
use Cache;

class MonobankApiCacheDecorator extends MonobankApi
{
    const TTL = 60; // 60 minutes

    /**
     * @inheritDoc
     */
    public function getRawClientInfo(): array
    {
        return $this->getCached(__FUNCTION__, 5);
    }

    /**
     * @inheritDoc
     */
    public function getRates(): array
    {
        return $this->getCached(__FUNCTION__);
    }

    /**
     * @param string $method
     * @param int|null $ttl
     * @return mixed
     */
    protected function getCached(string $method, ?int $ttl = null): mixed
    {
        $cached = Cache::get($method);

        if ($cached) {
            return unserialize($cached);
        } else {
            $res = parent::$method();
            Cache::put($method, serialize($res), now()->addMinutes($ttl ?? self::TTL));

            return $res;
        }
    }
}
