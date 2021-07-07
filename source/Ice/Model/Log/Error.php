<?php

namespace Ice\Model;

use Ice\Core\Model;

/**
 * Class Log_Error
 *
 * @property mixed log_error_pk
 * @property mixed ip
 * @property mixed agent
 * @property mixed referer
 * @property mixed host
 * @property mixed uri
 * @property mixed post__json
 * @property mixed exception__json
 *
 * @see \Ice\Core\Model
 *
 * @package Ice\Model
 */
class Log_Error extends Model
{
    protected static function config()
    {
        return [
            'dataSourceKey' => 'Ice\DataSource\Mysqli/default.moex',
            'scheme' => [
                'tableName' => 'ice_log_error',
                'engine' => 'InnoDB',
                'charset' => 'utf8_general_ci',
                'comment' => 'Журнал ошибок',
            ],
            'columns' => [
                'log_error_pk' => [
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
                    'fieldName' => 'log_error_pk',
                    'Ice\Widget\Model_Form' => 'Field_Number',
                    'Ice\Core\Validator' => [],
                    'Ice\Widget\Model_Table' => 'text',
                ],
                'error_created_at' => [
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
                        'name' => 'error_created_at',
                        'type' => 'date',
                        'validators' => [
                            0 => 'Ice:Not_Null',
                        ],
                    ],
                    'fieldName' => 'error_created_at',
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
                    'options' => [
                        'name' => 'exception',
                        'type' => 'textarea',
                        'validators' => [
                            'Ice:Length_Max' => 65535,
                        ],
                    ],
                    'fieldName' => 'exception',
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
                        'name' => 'session',
                        'type' => 'text',
                        'validators' => [
                            'Ice:Length_Max' => 64,
                        ],
                    ],
                    'fieldName' => 'session',
                ],
                'logger_class' => [
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
                        'name' => 'logger_class',
                        'type' => 'text',
                        'validators' => [
                            'Ice:Length_Max' => 255,
                            0 => 'Ice:Not_Null',
                        ],
                    ],
                    'fieldName' => 'logger_class',
                ],
                'environment' => [
                    'scheme' => [
                        'extra' => '',
                        'type' => 'varchar(12)',
                        'dataType' => 'varchar',
                        'length' => '12',
                        'characterSet' => 'utf8',
                        'nullable' => false,
                        'default' => null,
                        'comment' => '',
                    ],
                    'options' => [
                        'name' => 'environment',
                        'type' => 'text',
                        'validators' => [
                            'Ice:Length_Max' => 12,
                            0 => 'Ice:Not_Null',
                        ],
                    ],
                    'fieldName' => 'environment',
                ],
                'error_type' => [
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
                        'name' => 'error_type',
                        'type' => 'text',
                        'validators' => [
                            'Ice:Length_Max' => 255,
                            0 => 'Ice:Not_Null',
                        ],
                    ],
                    'fieldName' => 'error_type',
                ],
                'request_type' => [
                    'scheme' => [
                        'extra' => '',
                        'type' => 'varchar(4)',
                        'dataType' => 'varchar',
                        'length' => '4',
                        'characterSet' => 'utf8',
                        'nullable' => true,
                        'default' => null,
                        'comment' => '',
                    ],
                    'options' => [
                        'name' => 'request_type',
                        'type' => 'text',
                        'validators' => [
                            'Ice:Length_Max' => 4,
                        ],
                    ],
                    'fieldName' => 'request_type',
                ],
                'request_data__json' => [
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
                        'name' => 'request_data__json',
                        'type' => 'textarea',
                        'validators' => [
                            'Ice:Length_Max' => 65535,
                        ],
                    ],
                    'fieldName' => 'request_data__json',
                ],
                'request_string' => [
                    'scheme' => [
                        'extra' => '',
                        'type' => 'varchar(511)',
                        'dataType' => 'varchar',
                        'length' => '511',
                        'characterSet' => 'utf8',
                        'nullable' => true,
                        'default' => null,
                        'comment' => '',
                    ],
                    'options' => [
                        'name' => 'request_string',
                        'type' => 'text',
                        'validators' => [
                            'Ice:Length_Max' => 255,
                        ],
                    ],
                    'fieldName' => 'request_string',
                ],
                'error_context__json' => [
                    'scheme' => [
                        'extra' => '',
                        'type' => 'longtext',
                        'dataType' => 'longtext',
                        'length' => '4294967295',
                        'characterSet' => 'utf8',
                        'nullable' => false,
                        'default' => '[]',
                        'comment' => '',
                    ],
                    'options' => [
                        'name' => 'error_context__json',
                        'type' => 'textarea',
                        'validators' => [
                            'Ice:Length_Max' => 4294967295,
                        ],
                    ],
                    'fieldName' => 'error_context__json',
                ],
                'request_method' => [
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
                        'name' => 'request_method',
                        'type' => 'text',
                        'validators' => [
                            'Ice:Length_Max' => 255,
                        ],
                    ],
                    'fieldName' => 'request_method',
                ],
                'message' => [
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
                        'name' => 'message',
                        'type' => 'text',
                        'validators' => [
                            'Ice:Length_Max' => 255,
                            0 => 'Ice:Not_Null',
                        ],
                    ],
                    'fieldName' => 'message',
                ],
                'hostname' => [
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
                        'name' => 'hostname',
                        'type' => 'text',
                        'validators' => [
                            'Ice:Length_Max' => 255,
                            0 => 'Ice:Not_Null',
                        ],
                    ],
                    'fieldName' => 'hostname',
                ],
                'user_id' => [
                    'fieldName' => 'user_id',
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
                        'name' => 'user_id',
                        'type' => 'number',
                    ],
                ],
            ],
            'indexes' => [
                'PRIMARY KEY' => [
                    'PRIMARY' => [
                        1 => 'log_error_pk',
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
            'revision' => '05061618_mqw',
            'moduleAlias' => 'Ice',
            'modelClass' => 'Ice\Model\Log_Error',
        ];
    }
}