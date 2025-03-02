<?php
namespace App\Modules\Notifications\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;

class NotificationsServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('App\Modules\Notifications\Services\NotificationService');
        $this->app->singleton('App\Modules\Notifications\Services\EmailService');
        $this->app->singleton('App\Modules\Notifications\Services\GroupingService');
    }

    public function boot()
    {
        // Load routes

        $this->loadRoutesFrom(__DIR__ . '/../Routes/api.php');

        // Load views
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'notifications');

        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                \App\Modules\Notifications\Console\SendDailyDigest::class,
                \App\Modules\Notifications\Console\SendWeeklyDigest::class,
                \App\Modules\Notifications\Console\CleanupNotifications::class,
            ]);
        }

        // Publish assets
        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/notifications'),
            __DIR__ . '/../config/notifications.php' => config_path('notifications.php'),
        ]);
    }
}








