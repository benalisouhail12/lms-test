<?php

return [
    'name' => 'Authentication',

    'guard' => 'sanctum',

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => \App\Modules\Authentication\Models\User::class,
        ],
    ],

    'routes' => [
        'api' => [
            'prefix' => 'api/auth',
            'middleware' => ['api'],
        ],
        'web' => [
            'prefix' => 'auth',
            'middleware' => ['web'],
        ],
    ],

    'roles' => [
        'student',
        'teacher',
        'department_head',
        'admin',
        'guest'
    ],
];
