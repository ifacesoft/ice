<?php

return [
    'ice_admin_database' => [
        'route' => '',
        'request' => [
            'GET' => [
                'Ice:Layout_Admin' => [
                    'actions' => [
                        ['Ice:Title' => 'title', ['title' => 'Databases']],
                        'Ice:Admin_Database' => 'main'
                    ]
                ]
            ]
        ],
        'parent' => 'ice_admin'
    ],
    'ice_admin_database_dashboard' => [
        'route' => '',
        'request' => [
            'GET' => [
                'Ice:Layout_Admin' => [
                    'actions' => [
                        ['Ice:Title' => 'title', ['title' => 'Databases']],
                        'Ice:Admin_Database_Dashboard' => 'main'
                    ]
                ]
            ]
        ],
        'parent' => 'ice_admin_database'
    ],
];