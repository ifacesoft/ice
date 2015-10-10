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
                    'sidebar' => 'Ice:Admin_Database_Sidebar',
                    'main' => ['Ice:Admin_Block', ['dashboard' => 'Ice:Admin_Database_Dashboard']]
                ]
            ]
        ],
        'parent' => 'ice_admin_database'
    ],
    'ice_admin_database_database' => [
        'route' => '/{$dataSourceKey}',
        'params' => ['dataSourceKey' => '(\d+)'],
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
        'route' => '/{$dataSourceKey}/{$tableName}',
        'params' => [
            'dataSourceKey' => '(\d+)',
            'tableName' => '(.*)'
        ],
        'request' => [
            'GET' => [
                'widgetClass' => 'Ice:Admin_Layout',
                'widgetParams' => [
                    'sidebar' => 'Ice:Admin_Database_Sidebar',
                    'main' => ['Ice:Admin_Block', ['dashboard' => 'Ice:Admin_Database_Table']]
                ]
            ]
        ],
        'parent' => 'ice_admin_database_database'
    ],
    'ice_admin_database_row' => [
        'route' => '/{$dataSourceKey}/{$tableName}/{$pk}',
        'params' => [
            'dataSourceKey' => '(\d+)',
            'tableName' => '(.*)',
            'pk' => '(\d+)',
        ],
        'request' => [
            'GET' => [
                'widgetClass' => 'Ice:Admin_Layout',
                'widgetParams' => [
                    'sidebar' => 'Ice:Admin_Database_Sidebar',
                    'main' => ['Ice:Admin_Block', ['dashboard' => 'Ice:Admin_Database_Row']]
                ]
            ]
        ],
        'parent' => 'ice_admin_database_table'
    ],
];