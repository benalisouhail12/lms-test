<?php

namespace App\Modules\Authentication\Providers;

use Illuminate\Support\ServiceProvider;
use App\Modules\Authentication\Middleware\KeycloakAuthentication;
use App\Modules\Authentication\Services\KeycloakService;
use App\Modules\Authentication\Listeners\UserLoginListener;
use App\Modules\Authentication\Listeners\UserLogoutListener;
use App\Modules\Authentication\Listeners\UserRoleSyncListener;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;

class AuthenticationServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(KeycloakService::class, function ($app) {
            return new KeycloakService(
                config('keycloak.base_url'),
                config('keycloak.realm'),
                config('keycloak.client_id'),
                config('keycloak.client_secret')
            );
        });

        $this->mergeConfigFrom(
            __DIR__.'/../Config/keycloak.php', 'keycloak'
        );
    }

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/../Routes/api.php');
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'authentication');

        $this->publishes([
            __DIR__.'/../Config/keycloak.php' => config_path('keycloak.php'),
        ], 'auth-config');

        $this->app['router']->aliasMiddleware('keycloak', KeycloakAuthentication::class);

        $this->app['events']->listen(
            Login::class,
            UserLoginListener::class
        );

        $this->app['events']->listen(
            Logout::class,
            UserLogoutListener::class
        );

        $this->app['events']->listen(
            'auth.role.sync',
            UserRoleSyncListener::class
        );
    }
}
