<?php
return [
    'time' => '2015-02-15 21:05:27',
    'revision' => '02152105',
    'tables' => [
        'ice_account' => [
            'engine' => 'InnoDB',
            'charset' => 'utf8_general_ci',
            'comment' => '',
            'modelClass' => 'Ice\\Model\\Account',
            'revision' => '02152057',
        ],
        'ice_role' => [
            'engine' => 'InnoDB',
            'charset' => 'utf8_general_ci',
            'comment' => '',
            'modelClass' => 'Ice\\Model\\Role',
            'revision' => '02152057',
        ],
        'ice_test' => [
            'engine' => 'InnoDB',
            'charset' => 'utf8_general_ci',
            'comment' => '',
            'modelClass' => 'Ice\\Model\\Test',
            'revision' => '02152057',
        ],
        'ice_user' => [
            'engine' => 'InnoDB',
            'charset' => 'utf8_general_ci',
            'comment' => '',
            'modelClass' => 'Ice\\Model\\User',
            'revision' => '02152057',
        ],
        'ice_user_role_link' => [
            'engine' => 'InnoDB',
            'charset' => 'utf8_general_ci',
            'comment' => '',
            'modelClass' => 'Ice\\Model\\User_Role_Link',
            'revision' => '02152057',
        ],
    ],
];