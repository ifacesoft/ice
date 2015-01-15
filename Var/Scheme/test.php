<?php
return [
    'time' => '2015-01-14 15:32:43',
    'revision' => '01141532',
    'scheme' => 'test',
    'tables' => [
        'bi_blog' => [
            'engine' => 'InnoDB',
            'charset' => 'utf8_general_ci',
            'comment' => '',
            'modelClass' => 'Bi\\Model\\Blog',
            'revision' => '01141443',
        ],
        'bi_comment' => [
            'engine' => 'InnoDB',
            'charset' => 'utf8_general_ci',
            'comment' => '',
            'modelClass' => 'Bi\\Model\\Comment',
            'revision' => '01141443',
        ],
        'bi_post' => [
            'engine' => 'InnoDB',
            'charset' => 'utf8_general_ci',
            'comment' => '',
            'modelClass' => 'Bi\\Model\\Post',
            'revision' => '01141443',
        ],
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