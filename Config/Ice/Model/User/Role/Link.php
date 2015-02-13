<?php
return [
    'time' => '2015-02-13 10:14:26',
    'revision' => '02131014',
    'tableName' => 'ice_user_role_link',
    'dataSourceKey' => 'Ice\\Data\\Source\\Mysqli/default.test',
    'fields' => [
        'user__fk' => [
            'Ice\\Core\\Model_Scheme' => [
                'extra' => '',
                'type' => 'bigint(20)',
                'dataType' => 'bigint',
                'length' => '19,0',
                'characterSet' => null,
                'nullable' => false,
                'default' => null,
                'comment' => '',
                'columnName' => 'user__fk',
                'is_primary' => false,
                'is_foreign' => true,
            ],
            'Ice\\Core\\Data' => 'text',
            'Ice\\Core\\Form' => 'Number',
            'Ice\\Core\\Validator' => [
                0 => 'Ice:Not_Null',
            ],
        ],
        'role__fk' => [
            'Ice\\Core\\Model_Scheme' => [
                'extra' => '',
                'type' => 'bigint(20)',
                'dataType' => 'bigint',
                'length' => '19,0',
                'characterSet' => null,
                'nullable' => false,
                'default' => null,
                'comment' => '',
                'columnName' => 'role__fk',
                'is_primary' => false,
                'is_foreign' => true,
            ],
            'Ice\\Core\\Data' => 'text',
            'Ice\\Core\\Form' => 'Number',
            'Ice\\Core\\Validator' => [
                0 => 'Ice:Not_Null',
            ],
        ],
    ],
    'indexes' => [
        'PRIMARY KEY' => [
            'PRIMARY' => [
                1 => 'user__fk',
                2 => 'role__fk',
            ],
        ],
        'FOREIGN KEY' => [
            'fk_ice_user_role_link_ice_role' => [
                'fk_ice_user_role_link_ice_role' => 'role__fk',
            ],
            'user__fk' => [
                'fk_ice_user_role_link_ice_user' => 'user__fk',
            ],
        ],
        'UNIQUE' => [],
    ],
];