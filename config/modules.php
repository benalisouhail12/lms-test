<?php
// config/modules.php
return [
    'namespace' => 'App\\Modules',
    'modules' => [
        'Authentication' => [
            'enabled' => true,
            'provider' => 'App\\Modules\\Authentication\\Providers\\AuthenticationServiceProvider'
        ],
       'CourseManagement' => [
            'enabled' => true,
            'provider' => 'App\\Modules\\CourseManagement\\Providers\\CourseManagementServiceProvider'
        ],

        'StudentPortal' => [
            'enabled' => true,
            'provider' => 'App\\Modules\\StudentPortal\\Providers\\StudentPortalServiceProvider'
        ],

        'AssignmentSystem' => [
            'enabled' => true,
            'provider' => 'App\\Modules\\AssignmentSystem\\Providers\\AssignmentSystemServiceProvider'
        ],
        'Notifications' => [
            'enabled' => true,
            'provider' => 'App\\Modules\\Notifications\\Providers\\NotificationsServiceProvider'
        ],
        'Analytics' => [
            'enabled' => true,
            'provider' => 'App\\Modules\\Analytics\\Providers\\AnalyticsServiceProvider'
        ] 
    ]
];
