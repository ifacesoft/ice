<?php

return [
    'ice_security_login' => [
        'route' => '/login',
        'request' => [
            'GET' => [
                'Ice:Layout_Main' => [
                    'actions' => [
                        ['Ice:Title', ['title' => 'Login'], 'title'],
                        ['Ice:Security_Login', [], 'main']
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
                        ['Ice:Title', ['title' => 'Logout'], 'title'],
                        ['Ice:Security_Logout', [], 'main']
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
                        ['Ice:Title', ['title' => 'Register'], 'title'],
                        ['Ice:Security_Register', [], 'main']
                    ]
                ]
            ]
        ]
    ]
];