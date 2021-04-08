<?php

namespace Ice\Model;

use Ice\Core\Model;

/**
 * Class Ice_Session
 *
 * @property mixed session_pk
 * @property mixed session_data
 * @property mixed session_time
 * @property mixed session_lifetime
 *
 * @see \Ice\Core\Model
 *
 * @package Ice\Model
 */
class Session extends Model
{
    protected static function config()
    {
        return [
            'dataSourceKey' => 'Ice\DataSource\Mysqli/default.test',
            'scheme' => [
                'tableName' => 'ice_session',
                'engine' => 'InnoDB',
                'charset' => 'utf8_general_ci',
                'comment' => '',
            ],
            'columns' => [
                'session_pk' => [
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
                        'name' => 'session_pk',
                        'type' => 'text',
                        'validators' => [
                            'Ice:Length_Max' => 64,
                        ],
                    ],
                    'fieldName' => 'session_pk',
                ],
                'session_data' => [
                    'scheme' => [
                        'extra' => '',
                        'type' => 'blob',
                        'dataType' => 'blob',
                        'length' => '65535',
                        'characterSet' => null,
                        'nullable' => false,
                        'default' => null,
                        'comment' => '',
                    ],
                    'options' => [
                        'name' => 'session_data',
                        'type' => 'text',
                        'validators' => [
                            'Ice:Length_Max' => 65535,
                            0 => 'Ice:Not_Null',
                        ],
                    ],
                    'fieldName' => 'session_data',
                ],
                'session__fk' => [
                    'scheme' => [
                        'extra' => '',
                        'type' => 'varchar(64)',
                        'dataType' => 'varchar',
                        'length' => '64',
                        'characterSet' => 'utf8',
                        'nullable' => true,
                        'default' => null,
                        'comment' => '',
                    ],
                    'options' => [
                        'name' => 'session__fk',
                        'type' => 'text',
                        'validators' => [
                            'Ice:Length_Max' => 64,
                        ],
                    ],
                    'fieldName' => 'session__fk',
                ],
                'session_created_at' => [
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
                        'name' => 'session_created_at',
                        'type' => 'date',
                        'validators' => [
                            0 => 'Ice:Not_Null',
                        ],
                    ],
                    'fieldName' => 'session_created_at',
                ],
                'session_updated_at' => [
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
                        'name' => 'session_updated_at',
                        'type' => 'date',
                        'validators' => [
                            0 => 'Ice:Not_Null',
                        ],
                    ],
                    'fieldName' => 'session_updated_at',
                ],
                'session_deleted_at' => [
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
                        'name' => 'session_deleted_at',
                        'type' => 'date',
                        'validators' => [
                            0 => 'Ice:Not_Null',
                        ],
                    ],
                    'fieldName' => 'session_deleted_at',
                ],
                'session_lifetime' => [
                    'scheme' => [
                        'extra' => '',
                        'type' => 'mediumint(9)',
                        'dataType' => 'mediumint',
                        'length' => '7,0',
                        'characterSet' => null,
                        'nullable' => false,
                        'default' => null,
                        'comment' => '',
                    ],
                    'options' => [
                        'name' => 'session_lifetime',
                        'type' => 'text',
                        'validators' => [
                            'Ice:Length_Max' => 7,
                            0 => 'Ice:Not_Null',
                        ],
                    ],
                    'fieldName' => 'session_lifetime',
                ],
                'cookie_lifetime' => [
                    'scheme' => [
                        'extra' => '',
                        'type' => 'mediumint(9)',
                        'dataType' => 'mediumint',
                        'length' => '7,0',
                        'characterSet' => null,
                        'nullable' => false,
                        'default' => null,
                        'comment' => '',
                    ],
                    'options' => [
                        'name' => 'cookie_lifetime',
                        'type' => 'text',
                        'validators' => [
                            'Ice:Length_Max' => 7,
                            0 => 'Ice:Not_Null',
                        ],
                    ],
                    'fieldName' => 'cookie_lifetime',
                ],
                'ip' => [
                    'scheme' => [
                        'extra' => '',
                        'type' => 'varchar(15)',
                        'dataType' => 'varchar',
                        'length' => '15',
                        'characterSet' => 'utf8',
                        'nullable' => false,
                        'default' => null,
                        'comment' => '',
                    ],
                    'options' => [
                        'name' => 'ip',
                        'type' => 'text',
                        'validators' => [
                            'Ice:Length_Max' => 15,
                            0 => 'Ice:Not_Null',
                        ],
                    ],
                    'fieldName' => 'ip',
                ],
                'agent' => [
                    'scheme' => [
                        'extra' => '',
                        'type' => 'varchar(255)',
                        'dataType' => 'varchar',
                        'length' => '255',
                        'characterSet' => 'utf8',
                        'nullable' => true,
                        'default' => null,
                        'comment' => '',
                    ],
                    'options' => [
                        'name' => 'agent',
                        'type' => 'text',
                        'validators' => [
                            'Ice:Length_Max' => 255,
                            0 => 'Ice:Not_Null',
                        ],
                    ],
                    'fieldName' => 'agent',
                ],
                'user__fk' => [
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
                        'validators' => [
                            0 => 'Ice:Not_Null',
                        ],
                    ],
                    'fieldName' => 'user__fk',
                ],
                'views' => [
                    'scheme' => [
                        'extra' => '',
                        'type' => 'int(11)',
                        'dataType' => 'int',
                        'length' => '10,0',
                        'characterSet' => null,
                        'nullable' => false,
                        'default' => null,
                        'comment' => '',
                    ],
                    'options' => [
                        'name' => 'views',
                        'type' => 'number',
                        'validators' => [
                            0 => 'Ice:Not_Null',
                        ],
                    ],
                    'fieldName' => 'views',
                ],
            ],
            'indexes' => [
                'PRIMARY KEY' => [
                    'PRIMARY' => [
                        1 => 'session_pk',
                    ],
                ],
                'FOREIGN KEY' => [
                    'session__fk' => [
                        'FK_ice_session_ice_session' => 'session__fk',
                    ],
                ],
                'UNIQUE' => [],
            ],
            'references' => [
                'ice_session' => [
                    'constraintName' => 'FK_ice_session_ice_session',
                    'onUpdate' => 'CASCADE',
                    'onDelete' => 'CASCADE',
                ],
            ],
            'relations' => [
                'oneToMany' => [
                    'Ice\Model\Session' => 'session__fk',
                ],
                'manyToOne' => [
                    'Ice\Model\Session' => 'session__fk',
                ],
                'manyToMany' => [],
            ],
            'revision' => '06020843_n05',
            'moduleAlias' => 'Ice',
            'modelClass' => 'Ice\Model\Session',
        ];
    }
}