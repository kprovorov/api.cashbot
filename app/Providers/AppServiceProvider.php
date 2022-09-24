<?php

namespace App\Providers;

use App\Decorators\MonobankApiCacheDecorator;
use App\Services\CurrencyConverter;
use App\Services\MonobankApi;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;
use malkusch\lock\mutex\Mutex;
use malkusch\lock\mutex\PredisMutex;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            MonobankApi::class,
            MonobankApiCacheDecorator::class
        );

        $this->app->bind(Mutex::class, function (Container $app) {
            return new PredisMutex([
                $app->make('redis'),
            ], 'cashbot');
        });


        $this->app->singleton(CurrencyConverter::class);
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
