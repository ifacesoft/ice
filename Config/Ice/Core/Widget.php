<?php

return [
    'Ice\Widget\Admin_Navigation' => [
        'input' => [
            'routeNames' => [
                'default' => ['ice_admin_database']
            ]
        ]
    ],
    'Ice\Widget\Admin_Sidebar' => [
        'input' => [
            'routeNames' => [
                'default' => [
                    'ice_admin_database' => ['ice_admin_database_dashboard']
                ]
            ]
        ]
    ],
];