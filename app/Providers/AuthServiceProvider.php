<?php

namespace App\Providers;

use App\Policies\SystemPolicy;
use App\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
        User::class => SystemPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('isAdmin', function () {
//            $role = firebaseCreateData()->getReference('users/' . session()->get('userId') . '/role')->getValue();
            return true;
        });

        Gate::define('edit-settings', function ($user) {
            return true;
        });
    }
}
