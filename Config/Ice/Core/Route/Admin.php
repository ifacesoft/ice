<?php

return [
    'ice_admin' => [
        'route' => '',
        'redirect' => '_dashboard',
        'parent' => 'ice_main'
    ],
    'ice_admin_dashboard' => [
        'route' => '/dashboard',
        'request' => [
            'GET' => [
                'Ice:Layout_Admin' => [
                    'actions' => [
                        ['Ice:Title' => 'title', ['title' => 'Dashboard']],
                        'Ice:Admin_Dashboard' => 'main'
                    ]
                ]
            ]
        ],
        'parent' => 'ice_admin'
    ],
    '_Database' => '/database',
];