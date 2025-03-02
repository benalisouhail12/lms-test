<?php

namespace App\Modules\StudentPortal\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class StudentPortalServiceProvider extends ServiceProvider
{
    public function register()
    {
       /*  $this->mergeConfigFrom(
            __DIR__ . '/Config/config.php', 'student-portal'
        ); */
    }

    public function boot()
    {
        $this->registerRoutes();
        $this->registerViews();
        $this->registerMigrations();
        $this->registerTranslations();
        $this->loadJsonTranslationsFrom(__DIR__ . '/../Resources/lang');
    }

    protected function registerRoutes()
    {
        Route::middleware('web')
            ->namespace('App\Modules\StudentPortal\Http\Controllers')
            ->group(__DIR__ . '/../Routes/web.php');

        Route::prefix('api')
            ->middleware('api')
            ->namespace('App\Modules\StudentPortal\Http\Controllers')
            ->group(__DIR__ . '/../Routes/api.php');
    }

    protected function registerViews()
    {
/*         $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'student-portal');
 */    }

    protected function registerMigrations()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }

    protected function registerTranslations()
    {
        $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'student-portal');
    }
}
