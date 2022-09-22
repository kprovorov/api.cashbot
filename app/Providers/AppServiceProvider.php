<?php

namespace App\Providers;

use App\Decorators\MonobankApiCacheDecorator;
use App\Services\CurrencyConverter;
use App\Services\MonobankApi;
use Illuminate\Support\ServiceProvider;

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
