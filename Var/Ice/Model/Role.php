<?php
return [
    'time' => '2014-12-28 14:37:33',
    'revision' => '12281437',
    'scheme' => 'test',
    'modelClass' => 'Ice\\Model\\Role',
    'prefix' => 'ice',
    'columns' => [
        'role_pk' => [
            'extra' => 'auto_increment',
            'type' => 'bigint(20)',
            'dataType' => 'bigint',
            'length' => '19,0',
            'characterSet' => null,
            'nullable' => false,
            'default' => null,
            'comment' => '',
            'is_primary' => true,
            'is_foreign' => false,
        ],
        'role_name' => [
            'extra' => '',
            'type' => 'varchar(255)',
            'dataType' => 'varchar',
            'length' => '255',
            'characterSet' => 'utf8',
            'nullable' => true,
            'default' => null,
            'comment' => '',
            'is_primary' => false,
            'is_foreign' => false,
        ],
    ],
    'indexes' => [
        'PRIMARY KEY' => [
            'PRIMARY' => [
                1 => 'role_pk',
            ],
        ],
        'FOREIGN KEY' => [],
        'UNIQUE' => [],
    ],
];