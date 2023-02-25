<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        \App\UserModule\Models\User::class => \App\UserModule\Policies\UserPolicy::class,

        \App\AccountModule\Models\Jar::class => \App\AccountModule\Policies\JarPolicy::class,

        \App\TransferModule\Models\Transfer::class => \App\TransferModule\Policies\TransferPolicy::class,

        \App\AccountModule\Models\Account::class => \App\AccountModule\Policies\AccountPolicy::class,

        \App\PaymentModule\Models\Payment::class => \App\PaymentModule\Policies\PaymentPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
