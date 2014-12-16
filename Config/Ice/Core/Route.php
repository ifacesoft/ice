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