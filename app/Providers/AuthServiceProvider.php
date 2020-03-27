<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Auth;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use App\Auth\EloquentAdminUserProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
                // Binding eloquent.admin to our EloquentAdminUserProvider
               Auth::provider('eloquent.admin', function($app, array $config) {
                   return new EloquentAdminUserProvider($app['hash'], $config['model']);
               });
        //
    }
}
