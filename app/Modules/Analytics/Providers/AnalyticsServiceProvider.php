<?php
namespace app\Modules\Analytics\Providers;

use app\Modules\Analytics\Services\AnalyticsService;
use app\Modules\Analytics\Services\ExportService;
use Illuminate\Support\ServiceProvider;

class AnalyticsServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('analytics', function ($app) {
            return new AnalyticsService();
        });

        $this->app->singleton('analytics.export', function ($app) {
            return new ExportService();
        });
    }

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/../Routes/api.php');
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }
}
