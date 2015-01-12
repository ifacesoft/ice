<?php

return [
    'ice_main' => [
        'route' => '/',
        'GET' => [
            'actions' => [
                'title' => ['Ice:Title' => ['title' => 'Hello world']],
                'main' => 'Ice:Main'
            ]
        ]
    ],
    'ice_test' => [
        'route' => '/test',
        'GET' => [
            'actions' => [
                'testAction' => 'Ice:Test'
            ],
            'layout' => 'Ice:Layout_Test',
        ]
    ],
    '_Http_Status' => '',
    'ice_redirect_uri' => [
        'route' => '/redirect{$redirectUrl}',
        'GET' => [
            'redirect' => true
        ],
        'params' => [
            'redirectUrl' => '(/.*)',
        ]
    ],
    'ice_redirect' => [
        'route' => '/redirect',
        'GET' => [
            'redirect' => 'ice_main'
        ]
    ],
    '_Security' => '/security',
];