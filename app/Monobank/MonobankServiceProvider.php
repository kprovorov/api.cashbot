<?php

namespace App\Monobank;

use App\Monobank\Services\MonobankClient;
use App\Monobank\Services\MonobankClientCacheDecorator;
use GuzzleHttp\Client;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;
use malkusch\lock\mutex\PredisMutex;

class MonobankServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(
            MonobankClient::class,
            function (Container $app) {
                return new MonobankClientCacheDecorator(
                    new PredisMutex([
                        $app->make('redis'),
                    ], 'cashbot'),
                    new Client([
                        'base_uri' => config('services.monobank.base_url'),
                        'headers' => [
                            'X-Token' => config('services.monobank.token'),
                        ],
                    ])
                );
            }
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
