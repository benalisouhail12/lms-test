<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Notification Settings
    |--------------------------------------------------------------------------
    |
    | This option defines the default settings for notifications
    |
    */
    'default' => [
        'expiration' => [
            'enabled' => true,
            'days' => 30, // Notifications expire after 30 days by default
        ],
        'grouping' => [
            'enabled' => true,
            'max_per_group' => 10, // Maximum number of notifications per group
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Types
    |--------------------------------------------------------------------------
    |
    | Define different notification types and their default configurations
    |
    */
    'types' => [
        'system' => [
            'priority' => 'high',
            'default_channels' => ['email', 'webSocket'],
        ],
        'message' => [
            'priority' => 'medium',
            'default_channels' => ['email', 'webSocket', 'push'],
            'grouping' => true,
        ],
        'update' => [
            'priority' => 'low',
            'default_channels' => ['webSocket'],
            'grouping' => true,
        ],
        'activity' => [
            'priority' => 'low',
            'default_channels' => ['webSocket'],
            'grouping' => true,
        ],
        'module_complete' => [
            'priority' => 'medium',
            'default_channels' => ['email', 'webSocket'],
            'grouping' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | WebSocket Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for WebSocket connections
    |
    */
    'websocket' => [
        'reconnect_attempts' => 5,
        'reconnect_delay' => 3000, // 3 seconds
        'ping_interval' => 30000, // 30 seconds
    ],

    /*
    |--------------------------------------------------------------------------
    | Email Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for notification emails
    |
    */
    'email' => [
        'from' => [
            'address' => env('MAIL_FROM_ADDRESS', 'noreply@example.com'),
            'name' => env('MAIL_FROM_NAME', 'Notification System'),
        ],
        'digest' => [
            'daily_send_time' => '09:00', // Time of day to send daily digests (UTC)
            'weekly_send_day' => 1, // Day of week to send weekly digests (1 = Monday, 7 = Sunday)
            'weekly_send_time' => '09:00', // Time of day to send weekly digests (UTC)
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Frontend Config
    |--------------------------------------------------------------------------
    |
    | Configuration exposed to the frontend application
    |
    */
    'frontend' => [
        'poll_interval' => 60000, // 60 seconds, fallback if WebSocket fails
        'notification_display_time' => 5000, // Time to show notification toasts (ms)
        'max_displayed' => 5, // Maximum number of notifications to show in the dropdown
    ],
];
