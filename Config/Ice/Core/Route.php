<?php

return [
    'ice_main' => [
        'route' => '/',
        'request' => [
            'GET' => [
                'actions' => [
                    'title' => ['Ice:Title' => ['title' => 'Hello world']],
                    'main' => 'Ice:Main'
                ]
            ]
        ]
    ],
    'ice_test' => [
        'route' => '/test',
        'request' => [
            'GET' => [
                'layout' => 'Ice:Layout_Test',
                'actions' => [
                    'testAction' => 'Ice:Test'
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
            'GET' => [
                'blank' => 'Ice:Layout_Blank',
                'actions' => [
                    'content' => 'Ice:Locale'
                ]
            ]
        ]
    ],
    'ice_redirect' => [
        'route' => '/redirect',
        'request' => [
            'GET' => [
                'response' => [
                    'redirect' => 'ice_main'
                ]
            ]
        ]
    ],
    '_Security' => '/security',
];