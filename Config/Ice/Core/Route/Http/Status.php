<?php

return [
    'ice_404' => [
        'route' => '/404',
        'GET' => [
            'actions' => [
                'title' => ['Ice:Title' => ['title' => '404 Not Found']],
                'main' => 'Ice:Http_Status_404'
            ]
        ]
    ],
    'ice_500' => [
        'route' => '/500',
        'GET' => [
            'actions' => [
                'title' => ['Ice:Title' => ['title' => '500 Internal Server Error']],
                'main' => 'Ice:Http_Status_500'
            ]
        ]
    ]
];