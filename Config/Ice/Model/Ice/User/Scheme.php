<?php
return array(
    'user_pk' =>
        array(
            'type' => 'bigint(20)',
            'nullable' => false,
            'default' => null,
            'comment' => '',
        ),
    'is_active' =>
        array(
            'type' => 'tinyint(4)',
            'nullable' => false,
            'default' => '1',
            'comment' => '',
        ),
    'user_name' =>
        array(
            'type' => 'varchar(256)',
            'nullable' => false,
            'default' => null,
            'comment' => '',
        ),
    'user_surname' =>
        array(
            'type' => 'varchar(256)',
            'nullable' => false,
            'default' => null,
            'comment' => '',
        ),
    'data__json' =>
        array(
            'type' => 'text',
            'nullable' => false,
            'default' => '[]',
            'comment' => '',
        ),
);