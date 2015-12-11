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
                'actionClass' => 'Ice:Render',
                'widgetClass' => 'Ice:Admin_Layout',
                'widgetParams' => [
                    'sidebar' => 'Ice:Admin_Database_Sidebar',
                    'main' => ['Ice:Admin_Block', ['dashboard' => 'Ice:Admin_Database_Dashboard']]
                ]
            ]
        ],
        'parent' => 'ice_admin_database'
    ],
    'ice_admin_database_row' => [
        'route' => '/{$schemeName}/{$tableName}/{$pk}',
        'params' => [
            'schemeName' => '(\d+)',
            'tableName' => '(.*)',
            'pk' => '(\d+)',
        ],
        'request' => [
            'GET' => [
                'actionClass' => 'Ice:Render',
                'widgetClass' => 'Ice:Admin_Layout',
                'widgetParams' => [
                    'sidebar' => 'Ice:Admin_Database_Sidebar',
                    'main' => ['Ice:Admin_Block', ['model' => 'Ice:Admin_Database_Form']]
                ]
            ]
        ],
        'parent' => 'ice_admin_database_table'
    ],
    'ice_admin_database_table' => [
        'route' => '/{$schemeName}/{$tableName}',
        'params' => [
            'schemeName' => '(\d+)',
            'tableName' => '(.*)'
        ],
        'request' => [
            'GET' => [
                'actionClass' => 'Ice:Render',
                'widgetClass' => 'Ice:Admin_Layout',
                'widgetParams' => [
                    'sidebar' => 'Ice:Admin_Database_Sidebar',
                    'main' => ['Ice:Admin_Block', ['model' => 'Ice:Admin_Database_Table']]
                ]
            ]
        ],
        'parent' => 'ice_admin_database_database'
    ],
    'ice_admin_database_database' => [
        'route' => '/{$schemeName}',
        'params' => ['schemeName' => '(\d+)'],
        'request' => [
            'GET' => [
                'actionClass' => 'Ice:Render',
                'widgetClass' => 'Ice:Admin_Layout',
                'widgetParams' => [
                    'main' => 'Ice:Admin_Database_Database',
                    'sidebar' => 'Ice:Admin_Database_Sidebar'
                ]
            ]
        ],
        'parent' => 'ice_admin_database_dashboard'
    ],
];