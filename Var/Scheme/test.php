<?php
return [
    'time' => '2015-01-08 12:16:16',
    'revision' => '01081216',
    'scheme' => 'test',
    'tables' => [
        'ice_account' => [
            'engine' => 'InnoDB',
            'charset' => 'utf8_general_ci',
            'comment' => '',
            'modelClass' => 'Ice\\Model\\Account',
            'revision' => '12281437',
        ],
        'ice_role' => [
            'engine' => 'InnoDB',
            'charset' => 'utf8_general_ci',
            'comment' => '',
            'modelClass' => 'Ice\\Model\\Role',
            'revision' => '12281437',
        ],
        'ice_test' => [
            'engine' => 'InnoDB',
            'charset' => 'utf8_general_ci',
            'comment' => '',
            'modelClass' => 'Ice\\Model\\Test',
            'revision' => '01081216',
        ],
        'ice_user' => [
            'engine' => 'InnoDB',
            'charset' => 'utf8_general_ci',
            'comment' => '',
            'modelClass' => 'Ice\\Model\\User',
            'revision' => '12281437',
        ],
        'ice_user_role_link' => [
            'engine' => 'InnoDB',
            'charset' => 'utf8_general_ci',
            'comment' => '',
            'modelClass' => 'Ice\\Model\\User_Role_Link',
            'revision' => '12281437',
        ],
    ],
];