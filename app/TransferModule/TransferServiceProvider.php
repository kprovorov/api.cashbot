<?php

namespace App\TransferModule;

use Illuminate\Support\ServiceProvider;

class TransferServiceProvider extends ServiceProvider
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
            \App\TransferModule\Repositories\TransferRepo::class,
            \App\TransferModule\Repositories\EloquentTransferRepo::class
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
