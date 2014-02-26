<?php
return array(
    'account_pk' =>
        array(
            'type' => 'bigint(20)',
            'nullable' => false,
            'default' => null,
        ),
    'reg_date' =>
        array(
            'type' => 'timestamp',
            'nullable' => false,
            'default' => 'CURRENT_TIMESTAMP',
        ),
    'ip' =>
        array(
            'type' => 'varchar(32)',
            'nullable' => false,
            'default' => null,
        ),
    'account_type__fk' =>
        array(
            'type' => 'bigint(20)',
            'nullable' => true,
            'default' => null,
        ),
    'login' =>
        array(
            'type' => 'varchar(32)',
            'nullable' => false,
            'default' => null,
        ),
    'password' =>
        array(
            'type' => 'varchar(64)',
            'nullable' => false,
            'default' => null,
        ),
    'email' =>
        array(
            'type' => 'varchar(128)',
            'nullable' => false,
            'default' => null,
        ),
    'phone' =>
        array(
            'type' => 'varchar(11)',
            'nullable' => false,
            'default' => null,
        ),
    'user__fk' =>
        array(
            'type' => 'bigint(20)',
            'nullable' => true,
            'default' => null,
        ),
);