<?php
// app/Core/Providers/ModuleServiceProvider.php

namespace App\Core\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;

class ModuleServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->registerModules();
    }

    public function boot()
    {
        //
    }

    protected function registerModules()
    {
        $modules = config('modules.modules', []);

        foreach ($modules as $module => $config) {
            if ($config['enabled']) {
                $this->app->register($config['provider']);
            }
        }
    }
}
