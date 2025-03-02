<?php

return [
    App\Providers\AppServiceProvider::class,


    App\Core\Providers\ModuleServiceProvider::class,
    Laravel\Socialite\SocialiteServiceProvider::class,
    SocialiteProviders\Manager\ServiceProvider::class,
];
