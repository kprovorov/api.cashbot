<?php

namespace App\Providers;

use App\AccountModule\AccountServiceProvider;
use App\Monobank\MonobankServiceProvider;
use App\PaymentModule\PaymentServiceProvider;
use App\Services\CurrencyConverter;
use App\UserModule\UserServiceProvider;
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
        // Providers
        $this->app->register(UserServiceProvider::class);

        $this->app->register(AccountServiceProvider::class);

        $this->app->register(PaymentServiceProvider::class);

        $this->app->register(MonobankServiceProvider::class);

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
