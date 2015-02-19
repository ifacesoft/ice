<?php

return [
    'ice_security_login' => [
        'route' => '/login',
        'request' => [
            'GET' => [
                'Ice:Layout_Main' => [
                    'actions' => [
                        ['title' => 'Ice:Title', ['title' => 'Login']],
                        'main' => 'Ice:Security_Login',
                    ]
                ]
            ]
        ]
    ],
    'ice_security_logout' => [
        'route' => '/logout',
        'request' => [
            'GET' => [
                'Ice:Layout_Main' => [
                    'actions' => [
                        ['title' => 'Ice:Title', ['title' => 'Logout']],
                        'main' => 'Ice:Security_Logout'
                    ]
                ]
            ]
        ]
    ],
    'ice_security_register' => [
        'route' => '/register',
        'request' => [
            'GET' => [
                'Ice:Layout_Main' => [
                    'actions' => [
                        ['title' => 'Ice:Title', ['title' => 'Register']],
                        'main' => 'Ice:Security_Register'
                    ]
                ]
            ]
        ]
    ]
];