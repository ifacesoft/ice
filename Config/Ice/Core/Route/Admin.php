<?php

return [
    'ice_admin' => [
        'route' => '',
        'request' => [
            'GET' => [
                'Ice:Layout_Admin' => [
                    'actions' => [
                        ['Ice:Title' => 'title', ['title' => 'Databases']],
                        'Ice:Admin' => 'main'
                    ]
                ]
            ]
        ]
    ],
    '_Database' => '/database',
];