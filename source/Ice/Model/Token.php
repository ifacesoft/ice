<?php

namespace Ice\Model;

use Ice\Core\Model;

/**
 * Class Token
 *
 * @property mixed token_pk
 * @property mixed token
 * @property mixed token_created_at
 * @property mixed token_expired
 * @property mixed token_used_at
 * @property mixed token_data__json
 * @property mixed action_class
 * @property mixed user__fk
 *
 * @see \Ice\Core\Model
 *
 * @package Ice\Model
 */
class Token extends Model
{
    protected static function config()
    {
        return [
            'dataSourceKey' => 'Ice\DataSource\Mysqli/default.test',
            'scheme' => [
                'tableName' => 'ice_token',
                'engine' => 'InnoDB',
                'charset' => 'utf8_general_ci',
                'comment' => '',
            ],
            'columns' => [
                'id' => [
                    'fieldName' => 'token_pk',
                    'scheme' => [
                        'extra' => 'auto_increment',
                        'type' => 'bigint(20)',
                        'dataType' => 'bigint',
                        'length' => '19,0',
                        'characterSet' => null,
                        'nullable' => false,
                        'default' => null,
                        'comment' => '',
                    ],
                    'options' => [
                        'name' => 'token_pk',
                        'type' => 'number',
                    ],
                ],
                'token' => [
                    'fieldName' => 'token',
                    'scheme' => [
                        'extra' => '',
                        'type' => 'varchar(64)',
                        'dataType' => 'varchar',
                        'length' => '64',
                        'characterSet' => 'utf8',
                        'nullable' => false,
                        'default' => null,
                        'comment' => '',
                    ],
                    'options' => [
                        'name' => 'token',
                        'type' => 'text',
                        'validators' => [
                            'Ice:Length_Max' => 64,
                            0 => 'Ice:Not_Null',
                        ],
                    ],
                ],
                'token_created_at' => [
                    'fieldName' => 'token_created_at',
                    'scheme' => [
                        'extra' => '',
                        'type' => 'timestamp',
                        'dataType' => 'timestamp',
                        'length' => '0',
                        'characterSet' => null,
                        'nullable' => false,
                        'default' => 'CURRENT_TIMESTAMP',
                        'comment' => '',
                    ],
                    'options' => [
                        'name' => 'token_created_at',
                        'type' => 'date',
                        'validators' => [
                            0 => 'Ice:Not_Null',
                        ],
                    ],
                ],
                'token_expired_at' => [
                    'fieldName' => 'token_expired_at',
                    'scheme' => [
                        'extra' => '',
                        'type' => 'datetime',
                        'dataType' => 'datetime',
                        'length' => '0',
                        'characterSet' => null,
                        'nullable' => false,
                        'default' => null,
                        'comment' => '',
                    ],
                    'options' => [
                        'name' => 'token_expired_at',
                        'type' => 'date',
                        'validators' => [
                            0 => 'Ice:Not_Null',
                        ],
                    ],
                ],
                'token_used_at' => [
                    'fieldName' => 'token_used_at',
                    'scheme' => [
                        'extra' => '',
                        'type' => 'datetime',
                        'dataType' => 'datetime',
                        'length' => '0',
                        'characterSet' => null,
                        'nullable' => true,
                        'default' => null,
                        'comment' => '',
                    ],
                    'options' => [
                        'name' => 'token_used_at',
                        'type' => 'date',
                    ],
                ],
                'token_data__json' => [
                    'fieldName' => 'token_data__json',
                    'scheme' => [
                        'extra' => '',
                        'type' => 'text',
                        'dataType' => 'text',
                        'length' => '65535',
                        'characterSet' => 'utf8',
                        'nullable' => false,
                        'default' => '[]',
                        'comment' => '',
                    ],
                    'options' => [
                        'name' => 'token_data__json',
                        'type' => 'textarea',
                        'validators' => [
                            'Ice:Length_Max' => 65535,
                            0 => 'Ice:Not_Null',
                        ],
                    ],
                ],
                'action_class' => [
                    'fieldName' => 'action_class',
                    'scheme' => [
                        'extra' => '',
                        'type' => 'varchar(255)',
                        'dataType' => 'varchar',
                        'length' => '255',
                        'characterSet' => 'utf8',
                        'nullable' => false,
                        'default' => null,
                        'comment' => '',
                    ],
                    'options' => [
                        'name' => 'action_class',
                        'type' => 'text',
                        'validators' => [
                            'Ice:Length_Max' => 255,
                            0 => 'Ice:Not_Null',
                        ],
                    ],
                ],
                'user__fk' => [
                    'fieldName' => 'user__fk',
                    'scheme' => [
                        'extra' => '',
                        'type' => 'bigint(20)',
                        'dataType' => 'bigint',
                        'length' => '19,0',
                        'characterSet' => null,
                        'nullable' => true,
                        'default' => null,
                        'comment' => '',
                    ],
                    'options' => [
                        'name' => 'user__fk',
                        'type' => 'number',
                    ],
                ],
                'error' => [
                    'fieldName' => 'error',
                    'scheme' => [
                        'extra' => '',
                        'type' => 'text',
                        'dataType' => 'text',
                        'length' => '65535',
                        'characterSet' => 'utf8',
                        'nullable' => true,
                        'default' => null,
                        'comment' => '',
                    ],
                    'options' => [
                        'name' => 'error',
                        'type' => 'textarea',
                        'validators' => [
                            'Ice:Length_Max' => 65535,
                            0 => 'Ice:Not_Null',
                        ],
                    ],
                ],
            ],
            'indexes' => [
                'PRIMARY KEY' => [
                    'PRIMARY' => [
                        1 => 'id',
                    ],
                ],
                'FOREIGN KEY' => [
                    'user__fk' => [
                        'FK_ice_token_ice_user' => 'user__fk',
                    ],
                ],
                'UNIQUE' => [
                    'token' => [
                        1 => 'token',
                    ],
                ],
            ],
            'references' => [
                'ice_user' => [
                    'constraintName' => 'FK_ice_token_ice_user',
                    'onUpdate' => 'NO ACTION',
                    'onDelete' => 'NO ACTION',
                ],
            ],
            'relations' => [
                'oneToMany' => [
                    'Ice\Model\User' => 'user__fk',
                ],
                'manyToOne' => [],
                'manyToMany' => [],
            ],
            'revision' => '07061135_gzl',
            'moduleAlias' => 'Ice',
            'modelClass' => 'Ice\Model\Token',
        ];
    }
}