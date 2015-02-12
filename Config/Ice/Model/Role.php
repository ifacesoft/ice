<?php
return [
    'time' => '2015-02-12 18:29:16',
    'revision' => '02121829',
    'tableName' => 'ice_role',
    'dataSourceKey' => 'Ice\\Data\\Source\\Mysqli/default.test',
    'Ice\\Core\\Model' => [
        'role_pk' => 'role_pk',
        'role_name' => 'role_name',
    ],
    'Ice\\Core\\Model_Scheme' => [
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
                'is_primary' => false,
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
    ],
    'Ice\\Core\\Validator' => [
        'role_pk' => [
            0 => 'Ice:Not_Null',
        ],
        'role_name' => [],
    ],
    'Ice\\Core\\Form' => [
        'role_pk' => 'Number',
        'role_name' => 'Text',
    ],
    'Ice\\Core\\Data' => [
        'role_pk' => 'text',
        'role_name' => 'text',
    ],
];