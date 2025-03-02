<?php

namespace App\Modules\AssignmentSystem\Providers;

use Illuminate\Support\ServiceProvider;

class AssignmentSystemServiceProvider extends ServiceProvider
{
    /**
     * Enregistrement des services du module.
     */
    public function register()
    {

    }

    /**
     * Démarrage des services du module.
     */
    public function boot()
    {
        // Chargement des routes spécifiques au module
        $this->loadRoutesFrom(__DIR__.'/../Routes/api.php');

        // Chargement des migrations spécifiques au module
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');

        // Chargement des vues spécifiques au module
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'assignments');

        // Publication des configurations

    }
}
