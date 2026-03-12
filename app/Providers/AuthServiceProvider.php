<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        //
    ];

    public function boot(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Appointment Permissions
        |--------------------------------------------------------------------------
        */

        Gate::define('delete-appointments', function (User $user) {
            return $user->isSupervisor() || $user->isAdmin();
        });

    }
}
