<?php

namespace App\Services;

use App\DTO\MonobankClientInfoData;
use Http;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class MonobankApi
{
    /**
     * @return MonobankClientInfoData
     * @throws UnknownProperties
     */
    public function getClientInfo(): MonobankClientInfoData
    {
        $res = Http::withHeaders([
            'X-Token' => config('services.monobank.token'),
        ])->get('https://api.monobank.ua/personal/client-info')->json();

        return new MonoBankClientInfoData($res);
    }
}
