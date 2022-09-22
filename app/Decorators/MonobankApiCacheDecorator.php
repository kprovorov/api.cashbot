<?php

namespace App\Decorators;

use App\Services\MonobankApi;
use Cache;

class MonobankApiCacheDecorator extends MonobankApi
{
    const MONOBANK_CLIENT_INFO = 'MONOBANK_CLIENT_INFO';
    const MONOBANK_RATES = 'MONOBANK_RATES';

    /**
     * @inheritDoc
     */
    public function getRawClientInfo(): array
    {
        $cached = Cache::get(self::MONOBANK_CLIENT_INFO);

        if ($cached) {
            return $cached;
        } else {
            $clientInfo = parent::getRawClientInfo();
            Cache::put(self::MONOBANK_CLIENT_INFO, $clientInfo, now()->addMinutes(5));

            return $clientInfo;
        }
    }

    /**
     * @inheritDoc
     */
    public function getRates(): array
    {
        $cached = Cache::get(self::MONOBANK_RATES);

        if ($cached) {
            return $cached;
        } else {
            $rates = parent::getRates();
            Cache::put(self::MONOBANK_RATES, $rates, now()->addMinutes(5));

            return $rates;
        }
    }
}
