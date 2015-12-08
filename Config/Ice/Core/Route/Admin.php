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
                'widgetClass' => 'Ice:Admin_Layout',
                'widgetParams' => [
                    'main' => [
                        'Ice:Admin_Block',
                        [
                            'main' => 'Ice:Admin_Dashboard'
                        ]
                    ]
                ]
            ]
        ],
        'parent' => 'ice_admin'
    ],
    '_Database' => '/database',
];