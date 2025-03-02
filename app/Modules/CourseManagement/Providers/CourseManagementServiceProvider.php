<?php
// app/Modules/CourseManagement/Providers/CourseManagementServiceProvider.php
namespace App\Modules\CourseManagement\Providers;

use App\Modules\CourseManagement\Models\Course;
use App\Modules\CourseManagement\Policies\CoursePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class CourseManagementServiceProvider extends ServiceProvider
{
    protected $policies = [
        Course::class => CoursePolicy::class,
    ];
    public function register()
    {
        Gate::policies($this->policies);


    }

    public function boot()
    {
        // Charger les routes
        $this->registerRoutes();

        // Charger les migrations
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');


        // Publier les migrations
        $this->publishes([
            __DIR__.'/../Database/Migrations' => database_path('migrations'),
        ], 'migrations');
          // Publier les vues
          $this->loadViewsFrom(__DIR__.'/../Resources/views', 'CourseManagement');


        // Charger les traductions
        $this->loadTranslationsFrom(__DIR__.'/../Resources/lang', 'CourseManagement');
    }

    protected function registerRoutes()
    {
        Route::group($this->routeConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__.'/../Routes/api.php');
            $this->loadRoutesFrom(__DIR__.'/../Routes/web.php');
        });
    }

    protected function routeConfiguration()
    {
        return [
            'namespace' => 'App\Modules\CourseManagement\Controllers',
            'middleware' => ['api'],
            'prefix' => 'api/course'
        ];
    }
}
