<?php

return [
    'ice_private' => [
        'route' => '',
        'request' => [
            'GET' => [
                'Ice:Layout_Main' => [
                    'actions' => [
                        ['Ice:Title' => 'title', ['title' => 'Private']],
                        'Ice:Private' => 'main'
                    ]
                ]
            ]
        ]
    ],
    'ice_private_profile' => [
        'route' => '/profile',
        'request' => [
            'GET' => [
                'Ice:Layout_Main' => [
                    'actions' => [
                        ['Ice:Title' => 'title', ['title' => 'Profile']],
                        'Ice:Private_Profile' => 'main'
                    ]
                ]
            ]
        ]
    ]
];