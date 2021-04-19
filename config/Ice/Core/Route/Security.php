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
                'actionClass' => 'Ice\Action\Security_Login_Submit',
            ]
        ],
        'parent' => 'ice_security_login'
    ],
    'ice_security_logout' => [
        'route' => '/logout',
        'request' => [
            'GET' => [
                'actionClass' => 'Ice:Security_SignOut',
                'response' => ['contentType' => 'json']
            ]
        ],
        'parent' => 'ice_security'
    ],
    'ice_security_register' => [
        'route' => '/register',
        'request' => [
            'GET' => [
                'actionClass' => 'Ice:Render',
                'params' => [
                    'content' => [
                        'Ice:Layout_Main',
                        [
                            'main' => 'Ice:Security_Register',
                            'title' => ['Ice:Title', ['title' => 'Register']]
                        ]
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
                'actionClass' => 'Ice\Action\Security_Register_Submit',
            ]
        ],
        'parent' => 'ice_security_register'
    ],
    'ice_security_register_confirm' => [
        'route' => '/register/confirm',
        'request' => [
            'GET' => [
                'actionClass' => 'Ice:Render',
                'params' => [
                    'content' => [
                        'Ice:Layout_Main',
                        [
                            'main' => 'Ice:Security_RegisterConfirm',
                            'title' => ['Ice:Title', ['title' => 'Register confirm']]
                        ]
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
                'actionClass' => 'Ice\Action\Security_RegisterConfirm_Submit',
            ]
        ],
        'parent' => 'ice_security_register_confirm'
    ],
    'ice_security_restore_password' => [
        'route' => '/restore/password',
        'request' => [
            'GET' => [
                'actionClass' => 'Ice:Render',
                'params' => [
                    'content' => [
                        'Ice:Layout_Main',
                        [
                            'main' => 'Ice:Security_RestorePassword',
                            'title' => ['Ice:Title', ['title' => 'Restore password']]
                        ]
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
                'actionClass' => 'Ice\Action\Security_RestorePassword_Submit',
            ]
        ],
        'parent' => 'ice_security_restore_password'
    ],
    'ice_security_restore_password_confirm' => [
        'route' => '/restore/password/confirm',
        'request' => [
            'GET' => [
                'actionClass' => 'Ice:Render',
                'params' => [
                    'content' => [
                        'Ice:Layout_Main',
                        [
                            'main' => 'Ice:Security_RestorePasswordConfirm',
                            'title' => ['Ice:Title', ['title' => 'Confirm restore password']]
                        ]
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
                'actionClass' => 'Ice\Action\Security_RestorePasswordConfirm_Submit',
            ]
        ],
        'parent' => 'ice_security_login'
    ],
    'ice_security_change_email' => [
        'route' => '/change/email',
        'request' => [
            'GET' => [
                'actionClass' => 'Ice:Render',
                'params' => [
                    'content' => [
                        'Ice:Layout_Main',
                        [
                            'main' => 'Ice:Security_ChangeEmail',
                            'title' => ['Ice:Title', ['title' => 'Change Email']]
                        ]
                    ]
                ]
            ]
        ],
        'parent' => 'ice_security'
    ],
    'ice_security_change_email_request' => [
        'route' => '/change/email/confirm',
        'request' => [
            'POST' => [
                'actionClass' => 'Ice\Action\Security_ChangeEmail_Submit',
            ]
        ],
        'parent' => 'ice_security_change_email'
    ],
//    'ice_security_change_email_confirm_request' => [
//        'route' => '/change/email/confirm/request',
//        'request' => [
//            'POST' => [
//                'actionClass' => 'Ice\Action\Security_ChangeEmailConfirm_Submit',
//            ]
//        ],
//        'parent' => 'ice_security_login'
//    ],
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
                'actionClass' => 'Ice\Action\Security_ChangePassword_Submit',
            ]
        ],
        'parent' => 'ice_security_change_password'
    ],
];