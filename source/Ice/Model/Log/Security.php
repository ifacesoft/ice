<?php

namespace Ice\Model;

use Ice\Core\Model;

/**
 * Class Log_Security
 *
 * @property mixed log_security_pk
 * @property mixed create_time
 * @property mixed account_class
 * @property mixed account_key
 * @property mixed form_class
 * @property mixed error
 * @property mixed session__fk
 *
 * @see \Ice\Core\Model
 *
 * @package Ice\Model
 */
class Log_Security extends Model
{
    protected static function config()
    {
        return [
            'dataSourceKey' => 'Ice\DataSource\Mysqli/default.test',
            'scheme' => [
                'tableName' => 'ice_log_security',
                'engine' => 'InnoDB',
                'charset' => 'utf8_general_ci',
                'comment' => 'Журнал безопасности',
            ],
            'columns' => [
                'id' => [
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
                    'fieldName' => 'log_security_pk',
                    'options' => [
                        'name' => 'log_security_pk',
                        'type' => 'number',
                    ],
                ],
                'create_time' => [
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
                    'fieldName' => 'create_time',
                    'options' => [
                        'name' => 'create_time',
                        'type' => 'date',
                        'validators' => [
                            0 => 'Ice:Not_Null',
                        ],
                    ],
                ],
                'account_class' => [
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
                    'fieldName' => 'account_class',
                    'options' => [
                        'name' => 'account_class',
                        'type' => 'text',
                        'validators' => [
                            'Ice:Length_Max' => 255,
                            0 => 'Ice:Not_Null',
                        ],
                    ],
                ],
                'account_key' => [
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
                    'fieldName' => 'account_key',
                    'options' => [
                        'name' => 'account_key',
                        'type' => 'number',
                        'validators' => [
                            0 => 'Ice:Not_Null',
                        ],
                    ],
                ],
                'error' => [
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
                    'fieldName' => 'error',
                    'options' => [
                        'name' => 'error',
                        'type' => 'text',
                        'validators' => [
                            'Ice:Length_Max' => 255,
                            0 => 'Ice:Not_Null',
                        ],
                    ],
                ],
                'exception' => [
                    'scheme' => [
                        'extra' => '',
                        'type' => 'longtext',
                        'dataType' => 'longtext',
                        'length' => '4294967295',
                        'characterSet' => 'utf8',
                        'nullable' => true,
                        'default' => null,
                        'comment' => '',
                    ],
                    'fieldName' => 'exception',
                    'options' => [
                        'name' => 'exception',
                        'type' => 'textarea',
                        'validators' => [
                            'Ice:Length_Max' => 65535,
                        ],
                    ],
                ],
                'session' => [
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
                        'name' => 'session',
                        'type' => 'text',
                        'validators' => [
                            'Ice:Length_Max' => 64,
                        ],
                    ],
                    'fieldName' => 'session',
                ],
                'widget_class' => [
                    'fieldName' => 'widget_class',
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
                        'name' => 'widget_class',
                        'type' => 'text',
                        'validators' => [
                            'Ice:Length_Max' => 255,
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
            'references' => [],
            'relations' => [
                'oneToMany' => [],
                'manyToOne' => [],
                'manyToMany' => [],
            ],
            'revision' => '08191058_lnn',
            'moduleAlias' => 'Ice',
            'modelClass' => 'Ice\Model\Log_Security',
        ];
    }
}