<?php
return [
    'time' => '2015-02-12 18:29:16',
    'revision' => '02121829',
    'tableName' => 'ice_test',
    'dataSourceKey' => 'Ice\\Data\\Source\\Mysqli/default.test',
    'Ice\\Core\\Model' => [
        'id' => 'id',
        'test_name' => 'test_name',
        'name2' => 'name2',
    ],
    'Ice\\Core\\Model_Scheme' => [
        'columns' => [
            'id' => [
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
            'test_name' => [
                'extra' => '',
                'type' => 'varchar(50)',
                'dataType' => 'varchar',
                'length' => '50',
                'characterSet' => 'utf8',
                'nullable' => false,
                'default' => null,
                'comment' => '',
                'is_primary' => false,
                'is_foreign' => false,
            ],
            'name2' => [
                'extra' => '',
                'type' => 'varchar(50)',
                'dataType' => 'varchar',
                'length' => '50',
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
                    1 => 'id',
                ],
            ],
            'FOREIGN KEY' => [],
            'UNIQUE' => [],
        ],
    ],
    'Ice\\Core\\Validator' => [
        'id' => [
            0 => 'Ice:Not_Null',
        ],
        'test_name' => [
            0 => 'Ice:Not_Null',
        ],
        'name2' => [],
    ],
    'Ice\\Core\\Form' => [
        'id' => 'Number',
        'test_name' => 'Text',
        'name2' => 'Text',
    ],
    'Ice\\Core\\Data' => [
        'id' => 'text',
        'test_name' => 'text',
        'name2' => 'text',
    ],
];