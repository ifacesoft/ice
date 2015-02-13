<?php
return [
    'time' => '2015-02-13 10:14:26',
    'revision' => '02131014',
    'tableName' => 'ice_test',
    'dataSourceKey' => 'Ice\\Data\\Source\\Mysqli/default.test',
    'fields' => [
        'test_pk' => [
            'Ice\\Core\\Model_Scheme' => [
                'extra' => 'auto_increment',
                'type' => 'bigint(20)',
                'dataType' => 'bigint',
                'length' => '19,0',
                'characterSet' => null,
                'nullable' => false,
                'default' => null,
                'comment' => '',
                'columnName' => 'id',
                'is_primary' => true,
                'is_foreign' => false,
            ],
            'Ice\\Core\\Data' => 'text',
            'Ice\\Core\\Form' => 'Number',
            'Ice\\Core\\Validator' => [],
        ],
        'test_name' => [
            'Ice\\Core\\Model_Scheme' => [
                'extra' => '',
                'type' => 'varchar(50)',
                'dataType' => 'varchar',
                'length' => '50',
                'characterSet' => 'utf8',
                'nullable' => false,
                'default' => null,
                'comment' => '',
                'columnName' => 'test_name',
                'is_primary' => false,
                'is_foreign' => false,
            ],
            'Ice\\Core\\Data' => 'text',
            'Ice\\Core\\Form' => 'Text',
            'Ice\\Core\\Validator' => [
                0 => 'Ice:Not_Null',
            ],
        ],
        'name2' => [
            'Ice\\Core\\Model_Scheme' => [
                'extra' => '',
                'type' => 'varchar(50)',
                'dataType' => 'varchar',
                'length' => '50',
                'characterSet' => 'utf8',
                'nullable' => true,
                'default' => null,
                'comment' => '',
                'columnName' => 'name2',
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
                1 => 'id',
            ],
        ],
        'FOREIGN KEY' => [],
        'UNIQUE' => [],
    ],
];