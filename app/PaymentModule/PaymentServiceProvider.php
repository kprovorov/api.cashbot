<?php

namespace App\PaymentModule;

use App\PaymentModule\Repositories\EloquentGroupRepo;
use App\PaymentModule\Repositories\EloquentPaymentRepo;
use App\PaymentModule\Repositories\GroupRepo;
use App\PaymentModule\Repositories\PaymentRepo;
use Illuminate\Support\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Repositories
        $this->app->bind(
            GroupRepo::class,
            EloquentGroupRepo::class
        );

        $this->app->bind(
            PaymentRepo::class,
            EloquentPaymentRepo::class
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
