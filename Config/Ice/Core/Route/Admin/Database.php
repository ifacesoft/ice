<?php

return [
    'ice_admin_database' => [
        'route' => '',
        'request' => [
            'GET' => [
                'Ice:Layout_Admin' => [
                    'actions' => [
                        ['Ice:Title' => 'title', ['title' => 'Databases']],
                        'Ice:Admin_Database' => 'main'
                    ]
                ]
            ]
        ]
    ],
];