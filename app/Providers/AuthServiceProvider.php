<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\UserModule\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
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

        \App\AccountModule\Models\Account::class => \App\AccountModule\Policies\AccountPolicy::class,

        \App\PaymentModule\Models\Payment::class => \App\PaymentModule\Policies\PaymentPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        ResetPassword::createUrlUsing(function (User $user, string $token) {
            return env('WEB_APP_URL')."/auth/password/reset?token=$token&email=$user->email";
        });
    }
}
