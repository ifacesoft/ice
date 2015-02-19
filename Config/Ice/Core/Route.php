<?php

return [
    'ice_main' => [
        'route' => '/',
        'request' => [
            'GET' => [
                'Ice:Layout_Main' => [
                    'actions' => [
                        ['title' => 'Ice:Title', ['title' => 'Hello world']],
                        'main' => 'Ice:Main'
                    ]
                ]
            ]
        ]
    ],
    'ice_test' => [
        'route' => '/test',
        'request' => [
            'GET' => [
                'Ice:Layout_Test' => [
                    'actions' => [
                        'testAction' => 'Ice:Test'
                    ]
                ]
            ]
        ]
    ],
    'ice_locale' => [
        'route' => '/locale/{$locale}',
        'params' => [
            'locale' => '([a-z]+)',
        ],
        'request' => [
            'GET' => 'Ice:Locale'
        ]
    ],
    'ice_redirect' => [
        'route' => '/redirect',
        'request' => [
            'GET' => [
                'Ice:Layout_Blank' => [
                    'response' => [
                        'redirect' => 'ice_main'
                    ]
                ]
            ]
        ]
    ],
    '_Security' => '/security',
];