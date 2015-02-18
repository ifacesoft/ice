<?php

return [
    'ice_main' => [
        'route' => '/',
        'request' => [
            'GET' => [
                'Ice:Layout_Main' => [
                    'actions' => [
                        ['Ice:Title', ['title' => 'Hello world'], 'title'],
                        ['Ice:Main', [], 'main']
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
                        ['Ice:Test', [], 'testAction']
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