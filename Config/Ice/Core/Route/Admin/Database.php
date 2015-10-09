<?php

return [
    'ice_admin_database' => [
        'route' => '',
        'redirect' => '_dashboard',
        'parent' => 'ice_admin'
    ],
    'ice_admin_database_dashboard' => [
        'route' => '/dashboard',
        'request' => [
            'GET' => [
                'widgetClass' => 'Ice:Admin_Layout',
                'widgetParams' => [
                    'main' => 'Ice:Admin_Database_Dashboard',
                    'sidebar' => 'Ice:Admin_Database_Sidebar'
                ]
            ]
        ],
        'parent' => 'ice_admin_database'
    ],
    'ice_admin_database_database' => [
        'route' => '/{$dataSourceKeyCrc32}',
        'params' => ['dataSourceKeyCrc32' => '(\d+)'],
        'request' => [
            'GET' => [
                'widgetClass' => 'Ice:Admin_Layout',
                'widgetParams' => [
                    'main' => 'Ice:Admin_Database_Database',
                    'sidebar' => 'Ice:Admin_Database_Sidebar'
                ]
            ]
        ],
        'parent' => 'ice_admin_database_dashboard'
    ],
    'ice_admin_database_table' => [
        'route' => '/{$dataSourceKeyCrc32}/{$tableName}',
        'params' => [
            'dataSourceKeyCrc32' => '(\d+)',
            'tableName' => '(\d+)'
        ],
        'request' => [
            'GET' => [
                'widgetClass' => 'Ice:Admin_Layout',
                'widgetParams' => [
                    'main' => 'Ice:Admin_Database_Table',
                    'sidebar' => 'Ice:Admin_Database_Sidebar'
                ]
            ]
        ],
        'parent' => 'ice_admin_database_database'
    ],
];