<?php

return [
    'ice_admin' => [
        'route' => '',
        'redirect' => '_dashboard',
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
        ]
    ],
    '_Database' => '/database',
];