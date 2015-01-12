<?php
return [
    'time' => '2015-01-12 15:08:43',
    'revision' => '01121508',
    'scheme' => 'test',
    'tables' => [
        'bi_blog' => [
            'engine' => 'InnoDB',
            'charset' => 'utf8_general_ci',
            'comment' => '',
            'modelClass' => 'Bi\\Model\\Blog',
            'revision' => '12151537',
        ],
        'bi_comment' => [
            'engine' => 'InnoDB',
            'charset' => 'utf8_general_ci',
            'comment' => '',
            'modelClass' => 'Bi\\Model\\Comment',
            'revision' => '01121507',
        ],
        'bi_post' => [
            'engine' => 'InnoDB',
            'charset' => 'utf8_general_ci',
            'comment' => '',
            'modelClass' => 'Bi\\Model\\Post',
            'revision' => '12151537',
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