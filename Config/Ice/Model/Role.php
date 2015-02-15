<?php
return [
    'time' => '2015-02-15 20:57:42',
    'revision' => '02152057',
    'tableName' => 'ice_role',
    'dataSourceKey' => 'Ice\\Data\\Source\\Mysqli/default.test',
    'fields' => [
        'role_pk' => [
            'Ice\\Core\\Model_Scheme' => [
                'extra' => 'auto_increment',
                'type' => 'bigint(20)',
                'dataType' => 'bigint',
                'length' => '19,0',
                'characterSet' => null,
                'nullable' => false,
                'default' => null,
                'comment' => '',
                'columnName' => 'role_pk',
                'is_primary' => true,
                'is_foreign' => false,
            ],
            'Ice\\Core\\Data' => 'text',
            'Ice\\Core\\Form' => 'Number',
            'Ice\\Core\\Validator' => [],
        ],
        'role_name' => [
            'Ice\\Core\\Model_Scheme' => [
                'extra' => '',
                'type' => 'varchar(255)',
                'dataType' => 'varchar',
                'length' => '255',
                'characterSet' => 'utf8',
                'nullable' => true,
                'default' => null,
                'comment' => '',
                'columnName' => 'role_name',
                'is_primary' => false,
                'is_foreign' => false,
            ],
            'Ice\\Core\\Data' => 'text',
            'Ice\\Core\\Form' => 'Text',
            'Ice\\Core\\Validator' => [],
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