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