<?php

namespace App\AccountModule;

use Illuminate\Support\ServiceProvider;

class AccountServiceProvider extends ServiceProvider
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
            \App\AccountModule\Repositories\JarRepo::class,
            \App\AccountModule\Repositories\EloquentJarRepo::class
        );

        $this->app->bind(
            \App\AccountModule\Repositories\AccountRepo::class,
            \App\AccountModule\Repositories\EloquentAccountRepo::class
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
