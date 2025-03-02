<?php
return [
    /*
    |--------------------------------------------------------------------------
    | Configuration de Keycloak
    |--------------------------------------------------------------------------
    |
    | Ce fichier contient les paramètres de configuration pour l'intégration avec
    | Keycloak, y compris l'URL du serveur, les identifiants du client, les rôles,
    | et les paramètres des sessions.
    |
    */

    // URL du serveur Keycloak
    'base_url' => env('KEYCLOAK_BASE_URL', 'https://keycloak.example.com'),

    // Nom du Realm par défaut
    'realm' => env('KEYCLOAK_REALM', 'master'),

    // ID du client Keycloak
    'client_id' => env('KEYCLOAK_CLIENT_ID', 'lms-client'),

    // Secret du client Keycloak
    'client_secret' => env('KEYCLOAK_CLIENT_SECRET'),

    // URL de redirection après la connexion
    'redirect_uri' => env('KEYCLOAK_REDIRECT_URI', env('APP_URL') . '/auth/callback'),

    // URL de redirection après la déconnexion
    'logout_redirect_uri' => env('KEYCLOAK_LOGOUT_REDIRECT_URI', env('APP_URL')),

    // Durée de vie du token en secondes (par défaut 8 heures)
    'token_lifetime' => env('KEYCLOAK_TOKEN_LIFETIME', 28800),

    // Mappage des rôles Keycloak avec les rôles de l'application
    'role_mapping' => [
        'student' => 'student',                // Étudiant
        'teacher' => 'teacher',                // Enseignant
        'department_head' => 'department_head', // Chef de département
        'administrator' => 'administrator',    // Administrateur
        'guest' => 'guest',                    // Invité
    ],

    // Rôles par défaut à attribuer si aucun rôle n'est trouvé dans Keycloak
    'default_roles' => ['guest'],

    // Paramètres pour la gestion multi-tenant (multi-locataires)
    'multi_tenant' => [
        'enabled' => env('KEYCLOAK_MULTI_TENANT', true),       // Activer/Désactiver le multi-tenant
        'tenant_header' => env('KEYCLOAK_TENANT_HEADER', 'X-Tenant'), // En-tête HTTP pour identifier le tenant
        'default_tenant' => env('KEYCLOAK_DEFAULT_TENANT', 'default'), // Tenant par défaut
    ],

    // Paramètres des sessions utilisateur
    'session' => [
        'prefix' => 'keycloak_',                              // Préfixe des sessions
        'lifetime' => env('KEYCLOAK_SESSION_LIFETIME', 480),   // Durée de vie de la session (en minutes)
    ],

    // Paramètres du cache pour stocker les jetons et les données de l'utilisateur
    'cache' => [
        'enabled' => env('KEYCLOAK_CACHE_ENABLED', true),      // Activer/Désactiver le cache
        'prefix' => 'keycloak_',                               // Préfixe pour les clés de cache
        'ttl' => env('KEYCLOAK_CACHE_TTL', 60),                // Durée de vie du cache (en minutes)
    ],
];
return [
    /*
    |--------------------------------------------------------------------------
    | Configuration de Keycloak
    |--------------------------------------------------------------------------
    |
    | Ce fichier contient les paramètres de configuration pour l'intégration avec
    | Keycloak, y compris l'URL du serveur, les identifiants du client, les rôles,
    | et les paramètres des sessions.
    |
    */

    // URL du serveur Keycloak
    'base_url' => env('KEYCLOAK_BASE_URL', 'https://keycloak.example.com'),

    // Nom du Realm par défaut
    'realm' => env('KEYCLOAK_REALM', 'master'),

    // ID du client Keycloak
    'client_id' => env('KEYCLOAK_CLIENT_ID', 'lms-client'),

    // Secret du client Keycloak
    'client_secret' => env('KEYCLOAK_CLIENT_SECRET'),

    // URL de redirection après la connexion
    'redirect_uri' => env('KEYCLOAK_REDIRECT_URI', env('APP_URL') . '/auth/callback'),

    // URL de redirection après la déconnexion
    'logout_redirect_uri' => env('KEYCLOAK_LOGOUT_REDIRECT_URI', env('APP_URL')),

    // Durée de vie du token en secondes (par défaut 8 heures)
    'token_lifetime' => env('KEYCLOAK_TOKEN_LIFETIME', 28800),

    // Mappage des rôles Keycloak avec les rôles de l'application
    'role_mapping' => [
        'student' => 'student',                // Étudiant
        'teacher' => 'teacher',                // Enseignant
        'department_head' => 'department_head', // Chef de département
        'administrator' => 'administrator',    // Administrateur
        'guest' => 'guest',                    // Invité
    ],

    // Rôles par défaut à attribuer si aucun rôle n'est trouvé dans Keycloak
    'default_roles' => ['guest'],

    // Paramètres pour la gestion multi-tenant (multi-locataires)
    'multi_tenant' => [
        'enabled' => env('KEYCLOAK_MULTI_TENANT', true),       // Activer/Désactiver le multi-tenant
        'tenant_header' => env('KEYCLOAK_TENANT_HEADER', 'X-Tenant'), // En-tête HTTP pour identifier le tenant
        'default_tenant' => env('KEYCLOAK_DEFAULT_TENANT', 'default'), // Tenant par défaut
    ],

    // Paramètres des sessions utilisateur
    'session' => [
        'prefix' => 'keycloak_',                              // Préfixe des sessions
        'lifetime' => env('KEYCLOAK_SESSION_LIFETIME', 480),   // Durée de vie de la session (en minutes)
    ],

    // Paramètres du cache pour stocker les jetons et les données de l'utilisateur
    'cache' => [
        'enabled' => env('KEYCLOAK_CACHE_ENABLED', true),      // Activer/Désactiver le cache
        'prefix' => 'keycloak_',                               // Préfixe pour les clés de cache
        'ttl' => env('KEYCLOAK_CACHE_TTL', 60),                // Durée de vie du cache (en minutes)
    ],
];
