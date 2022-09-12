<?php

namespace App\Decorators;

use App\Services\MonobankApi;
use Cache;

class MonobankApiCacheDecorator extends MonobankApi
{
    const MONOBANK_CLIENT_INFO = 'MONOBANK_CLIENT_INFO';

    public function getRawClientInfo(): array
    {
        $cached = Cache::get(self::MONOBANK_CLIENT_INFO);

        if ($cached) {
            return $cached;
        } else{
            $clientInfo = parent::getRawClientInfo();
            Cache::put(self::MONOBANK_CLIENT_INFO, $clientInfo, now()->addMinutes(5));

            return $clientInfo;
        }
    }
}
