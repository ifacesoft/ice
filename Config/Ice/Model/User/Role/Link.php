<?php
return [
    'time' => '2015-02-12 18:29:16',
    'revision' => '02121829',
    'tableName' => 'ice_user_role_link',
    'dataSourceKey' => 'Ice\\Data\\Source\\Mysqli/default.test',
    'Ice\\Core\\Model' => [
        'user__fk' => 'user__fk',
        'role__fk' => 'role__fk',
    ],
    'Ice\\Core\\Model_Scheme' => [
        'columns' => [
            'user__fk' => [
                'extra' => '',
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
            'role__fk' => [
                'extra' => '',
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
    ],
    'Ice\\Core\\Validator' => [
        'user__fk' => [
            0 => 'Ice:Not_Null',
        ],
        'role__fk' => [
            0 => 'Ice:Not_Null',
        ],
    ],
    'Ice\\Core\\Form' => [
        'user__fk' => 'Number',
        'role__fk' => 'Number',
    ],
    'Ice\\Core\\Data' => [
        'user__fk' => 'text',
        'role__fk' => 'text',
    ],
];