<?php

return [
    'ice_security_login' => [
        'route' => '/login',
        'request' => [
            'GET' => [
                'actionClass' => 'Ice:Render',
                'params' => [
                    'content' => [
                        'Ice:Layout_Main',
                        [
                            'main' => 'Ice:Security_Login',
                            'title' => ['Ice:Title', ['title' => 'Login']]
                        ]
                    ]
                ]
            ]
        ],
        'parent' => 'ice_security'
    ],
    'ice_security_login_request' => [
        'route' => '/login/request',
        'request' => [
            'POST' => [
                'actionClass' => 'Ice:Security_Login_Submit',
            ]
        ],
        'parent' => 'ice_security_login'
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
        ],
        'parent' => 'ice_security'
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
        ],
        'parent' => 'ice_security'
    ],
    'ice_security_register_request' => [
        'route' => '/register/request',
        'request' => [
            'POST' => [
                'actionClass' => 'Ice:Security_Register_Submit',
            ]
        ],
        'parent' => 'ice_security_register'
    ],
    'ice_security_register_confirm' => [
        'route' => '/register/confirm',
        'request' => [
            'GET' => [
                'Ice:Layout_Main' => [
                    'actions' => [
                        ['Ice:Title' => 'title', ['title' => 'Confirm']],
                        'Ice:Security_RegisterConfirm' => 'main'
                    ]
                ]
            ]
        ],
        'parent' => 'ice_security'
    ],
    'ice_security_register_confirm_request' => [
        'route' => '/register/confirm/request',
        'request' => [
            'POST' => [
                'actionClass' => 'Ice:Security_RegisterConfirm_Submit',
            ]
        ],
        'parent' => 'ice_security_register_confirm'
    ],
    'ice_security_restore_password' => [
        'route' => '/restore/password',
        'request' => [
            'GET' => [
                'Ice:Layout_Main' => [
                    'actions' => [
                        ['Ice:Title' => 'title', ['title' => 'Restore request']],
                        'Ice:Security_RestorePassword' => 'main'
                    ]
                ]
            ]
        ],
        'parent' => 'ice_security'
    ],
    'ice_security_restore_password_request' => [
        'route' => '/restore/password/request',
        'request' => [
            'POST' => [
                'actionClass' => 'Ice:Security_RestorePassword_Submit',
            ]
        ],
        'parent' => 'ice_security_restore_password'
    ],
    'ice_security_restore_password_confirm' => [
        'route' => '/restore/password/confirm',
        'request' => [
            'GET' => [
                'Ice:Layout_Main' => [
                    'actions' => [
                        ['Ice:Title' => 'title', ['title' => 'Restore confirm']],
                        'Ice:Security_RestorePasswordConfirm' => 'main'
                    ]
                ]
            ]
        ],
        'parent' => 'ice_security'
    ],
    'ice_security_restore_password_confirm_request' => [
        'route' => '/restore/password/confirm/request',
        'request' => [
            'POST' => [
                'actionClass' => 'Ice:Security_RestorePasswordConfirm_Submit',
            ]
        ],
        'parent' => 'ice_security_login'
    ],
    'ice_security_change_password' => [
        'route' => '/change/password',
        'request' => [
            'GET' => [
                'actionClass' => 'Ice:Render',
                'params' => [
                    'content' => [
                        'Ice:Layout_Main',
                        [
                            'main' => 'Ice:Security_ChangePassword',
                            'title' => ['Ice:Title', ['title' => 'Change password']]
                        ]
                    ]
                ]
            ]
        ],
        'parent' => 'ice_security'
    ],
    'ice_security_change_password_request' => [
        'route' => '/change/password/request',
        'request' => [
            'POST' => [
                'actionClass' => 'Ice:Security_ChangePassword_Submit',
            ]
        ],
        'parent' => 'ice_security_change_password'
    ],
];