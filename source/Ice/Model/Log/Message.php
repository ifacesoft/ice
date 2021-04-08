<?php

namespace Ice\Model;

use Ice\Core\Model;

/**
 * Class Log_Message
 *
 * @property mixed log_message_pk
 * @property mixed create_time
 * @property mixed message_class
 * @property mixed address
 * @property mixed name
 * @property mixed subject
 * @property mixed body
 * @property mixed address_type
 * @property mixed from_address
 * @property mixed from_name
 * @property mixed success_time
 * @property mixed attampt
 * @property mixed session__fk
 *
 * @see \Ice\Core\Model
 *
 * @package Ice\Model
 */
class Log_Message extends Model
{
    protected static function config()
    {
        return [
            'dataSourceKey' => 'Ice\DataSource\Mysqli/default.test',
            'scheme' => [
                'tableName' => 'ice_log_message',
                'engine' => 'InnoDB',
                'charset' => 'utf8_general_ci',
                'comment' => 'Журнал сообщений',
            ],
            'columns' => [
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
                'message_class' => [
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
                    'fieldName' => 'message_class',
                    'options' => [
                        'name' => 'message_class',
                        'type' => 'text',
                        'validators' => [
                            'Ice:Length_Max' => 255,
                            0 => 'Ice:Not_Null',
                        ],
                    ],
                ],
                'address' => [
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
                    'fieldName' => 'address',
                    'options' => [
                        'name' => 'address',
                        'type' => 'text',
                        'validators' => [
                            'Ice:Length_Max' => 255,
                            0 => 'Ice:Not_Null',
                        ],
                    ],
                ],
                'name' => [
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
                    'fieldName' => 'name',
                    'options' => [
                        'name' => 'name',
                        'type' => 'text',
                        'validators' => [
                            'Ice:Length_Max' => 255,
                            0 => 'Ice:Not_Null',
                        ],
                    ],
                ],
                'subject' => [
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
                    'fieldName' => 'subject',
                    'options' => [
                        'name' => 'subject',
                        'type' => 'text',
                        'validators' => [
                            'Ice:Length_Max' => 255,
                            0 => 'Ice:Not_Null',
                        ],
                    ],
                ],
                'body' => [
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
                    'fieldName' => 'body',
                    'options' => [
                        'name' => 'body',
                        'type' => 'textarea',
                        'validators' => [
                            'Ice:Length_Max' => 65535,
                            0 => 'Ice:Not_Null',
                        ],
                    ],
                ],
                'from_address' => [
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
                    'fieldName' => 'from_address',
                    'options' => [
                        'name' => 'from_address',
                        'type' => 'text',
                        'validators' => [
                            'Ice:Length_Max' => 255,
                            0 => 'Ice:Not_Null',
                        ],
                    ],
                ],
                'from_name' => [
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
                    'fieldName' => 'from_name',
                    'options' => [
                        'name' => 'from_name',
                        'type' => 'text',
                        'validators' => [
                            'Ice:Length_Max' => 255,
                            0 => 'Ice:Not_Null',
                        ],
                    ],
                ],
                'success_time' => [
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
                    'fieldName' => 'success_time',
                    'options' => [
                        'name' => 'success_time',
                        'type' => 'date',
                    ],
                ],
                'attampt' => [
                    'scheme' => [
                        'extra' => '',
                        'type' => 'int(11)',
                        'dataType' => 'int',
                        'length' => '10,0',
                        'characterSet' => null,
                        'nullable' => false,
                        'default' => '1',
                        'comment' => '',
                    ],
                    'fieldName' => 'attampt',
                    'options' => [
                        'name' => 'attampt',
                        'type' => 'number',
                        'validators' => [
                            0 => 'Ice:Not_Null',
                        ],
                    ],
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
                    'fieldName' => 'logger_class',
                    'options' => [
                        'name' => 'logger_class',
                        'type' => 'text',
                        'validators' => [
                            'Ice:Length_Max' => 255,
                            0 => 'Ice:Not_Null',
                        ],
                    ],
                ],
                'to__json' => [
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
                    'fieldName' => 'to__json',
                    'options' => [
                        'name' => 'to__json',
                        'type' => 'textarea',
                        'validators' => [
                            'Ice:Length_Max' => 65535,
                        ],
                    ],
                ],
                'cc__json' => [
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
                    'fieldName' => 'cc__json',
                    'options' => [
                        'name' => 'cc__json',
                        'type' => 'textarea',
                        'validators' => [
                            'Ice:Length_Max' => 65535,
                        ],
                    ],
                ],
                'bcc__json' => [
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
                    'fieldName' => 'bcc__json',
                    'options' => [
                        'name' => 'bcc__json',
                        'type' => 'textarea',
                        'validators' => [
                            'Ice:Length_Max' => 65535,
                        ],
                    ],
                ],
                'recipient_count' => [
                    'scheme' => [
                        'extra' => '',
                        'type' => 'int(11)',
                        'dataType' => 'int',
                        'length' => '10,0',
                        'characterSet' => null,
                        'nullable' => false,
                        'default' => '1',
                        'comment' => '',
                    ],
                    'fieldName' => 'recipient_count',
                    'options' => [
                        'name' => 'recipient_count',
                        'type' => 'number',
                        'validators' => [
                            0 => 'Ice:Not_Null',
                        ],
                    ],
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
                'log_message_pk' => [
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
                        'name' => 'log_message_pk',
                        'type' => 'number',
                    ],
                    'fieldName' => 'log_message_pk',
                ],
            ],
            'indexes' => [
                'PRIMARY KEY' => [
                    'PRIMARY' => [
                        1 => 'log_message_pk',
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
            'revision' => '08191058_9vj',
            'moduleAlias' => 'Ice',
            'modelClass' => 'Ice\Model\Log_Message',
        ];
    }
}