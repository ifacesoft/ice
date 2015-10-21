<?php

return [
    'ice_security_login' => [
        'route' => '/login',
        'request' => [
            'GET' => [
                'Ice:Layout_Main' => [
                    'actions' => [
                        ['Ice:Title' => 'title', ['title' => 'Login']],
                        'Ice:Security_Login' => 'main'
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
                        ['Ice:Title' => 'title', ['title' => 'Logout']],
                        'Ice:Security_Logout' => 'main'
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
                        ['Ice:Title' => 'title', ['title' => 'Register']],
                        'Ice:Security_Register' => 'main'
                    ]
                ]
            ]
        ]
    ],
    'ice_security_confirm' => [
        'route' => '/confirm',
        'request' => [
            'GET' => [
                'Ice:Layout_Main' => [
                    'actions' => [
                        ['Ice:Title' => 'title', ['title' => 'Confirm']],
                        'Ice:Security_Confirm' => 'main'
                    ]
                ]
            ]
        ]
    ],
    'ice_security_restore_request' => [
        'route' => '/restore/request',
        'request' => [
            'GET' => [
                'Ice:Layout_Main' => [
                    'actions' => [
                        ['Ice:Title' => 'title', ['title' => 'Restore request']],
                        'Ice:Security_RestoreRequest' => 'main'
                    ]
                ]
            ]
        ]
    ],
    'ice_security_restore_confirm' => [
        'route' => '/restore/confirm',
        'request' => [
            'GET' => [
                'Ice:Layout_Main' => [
                    'actions' => [
                        ['Ice:Title' => 'title', ['title' => 'Restore confirm']],
                        'Ice:Security_RestoreConfirm' => 'main'
                    ]
                ]
            ]
        ]
    ]
];