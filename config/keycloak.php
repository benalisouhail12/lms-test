<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Keycloak Configuration
    |--------------------------------------------------------------------------
    */

    // Keycloak server URL
    'base_url' => env('KEYCLOAK_BASE_URL', 'https://keycloak.example.com'),

    // Default Realm
    'realm' => env('KEYCLOAK_REALM', 'master'),

    // Client ID
    'client_id' => env('KEYCLOAK_CLIENT_ID', 'lms-client'),

    // Client Secret
    'client_secret' => env('KEYCLOAK_CLIENT_SECRET'),

    // Redirect URI after login
    'redirect_uri' => env('KEYCLOAK_REDIRECT_URI', env('APP_URL') . '/auth/callback'),

    // Redirect URI after logout
    'logout_redirect_uri' => env('KEYCLOAK_LOGOUT_REDIRECT_URI', env('APP_URL')),

    // Token lifetime in seconds
    'token_lifetime' => env('KEYCLOAK_TOKEN_LIFETIME', 28800), // 8 hours

    // Role mapping from Keycloak to application roles
    'role_mapping' => [
        'student' => 'student',
        'teacher' => 'teacher',
        'department_head' => 'department_head',
        'administrator' => 'administrator',
        'guest' => 'guest',
    ],

    // Default roles to assign if no roles are found in Keycloak
    'default_roles' => ['guest'],

    // Multi-tenant settings
    'multi_tenant' => [
        'enabled' => env('KEYCLOAK_MULTI_TENANT', true),
        'tenant_header' => env('KEYCLOAK_TENANT_HEADER', 'X-Tenant'),
        'default_tenant' => env('KEYCLOAK_DEFAULT_TENANT', 'default'),
    ],

    // Session settings
    'session' => [
        'prefix' => 'keycloak_',
        'lifetime' => env('KEYCLOAK_SESSION_LIFETIME', 480), // 8 hours
    ],

    // Cache settings
    'cache' => [
        'enabled' => env('KEYCLOAK_CACHE_ENABLED', true),
        'prefix' => 'keycloak_',
        'ttl' => env('KEYCLOAK_CACHE_TTL', 60), // 60 minutes
    ],
];
