<?php
return array(
    'session_pk' =>
        array(
            'type' => 'varchar(64)',
            'nullable' => false,
            'default' => null,
            'comment' => '',
        ),
    'last_active' =>
        array(
            'type' => 'timestamp',
            'nullable' => false,
            'default' => '0000-00-00 00:00:00',
            'comment' => '',
        ),
    'ip' =>
        array(
            'type' => 'varchar(40)',
            'nullable' => false,
            'default' => null,
            'comment' => '',
        ),
    'user_agent' =>
        array(
            'type' => 'varchar(256)',
            'nullable' => false,
            'default' => null,
            'comment' => '',
        ),
    'start_time' =>
        array(
            'type' => 'timestamp',
            'nullable' => false,
            'default' => null,
            'comment' => '',
        ),
    'auth_date' =>
        array(
            'type' => 'timestamp',
            'nullable' => false,
            'default' => '0000-00-00 00:00:00',
            'comment' => '',
        ),
    'user__fk' =>
        array(
            'type' => 'bigint(20)',
            'nullable' => true,
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
    'city__fk' =>
        array(
            'type' => 'bigint(20)',
            'nullable' => true,
            'default' => null,
            'comment' => '',
        ),
);