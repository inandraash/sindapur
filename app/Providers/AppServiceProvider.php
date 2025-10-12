<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('isAdmin', function (User $user) {

            return $user->role->nama_role == 'Admin';
        });

        Gate::define('isStafDapur', function (User $user) {
            return $user->role->nama_role == 'Staf Dapur';
        });
    }
}
