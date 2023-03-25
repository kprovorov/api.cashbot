<?php

namespace App\Providers;

use App\AccountModule\AccountServiceProvider;
use App\Http\Integrations\Monobank\Monobank;
use App\PaymentModule\PaymentServiceProvider;
use App\Services\CurrencyConverter;
use App\UserModule\UserServiceProvider;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Providers
        $this->app->register(UserServiceProvider::class);

        $this->app->register(AccountServiceProvider::class);

        $this->app->register(PaymentServiceProvider::class);

        $this->app->singleton(CurrencyConverter::class);

        $this->app->bind(Monobank::class, function () {
            return new Monobank(
                config('services.monobank.token'),
                config('services.monobank.base_url')
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
