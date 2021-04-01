<?php

namespace Ice\Model;

use Ice\Core\Model;

/**
 * Class Message_Template
 *
 * @property mixed message_template_pk
 * @property mixed message_template_name
 * @property mixed subject
 * @property mixed body
 *
 * @see \Ice\Core\Model
 *
 * @package Ice\Model
 */
class Message_Template extends Model
{
    protected static function config()
    {
        return [
            'dataSourceKey' => 'Ice\DataSource\Mysqli/default.test',
            'scheme' => [
                'tableName' => 'ice_message_template',
                'engine' => 'InnoDB',
                'charset' => 'utf8_general_ci',
                'comment' => '',
            ],
            'columns' => [
                'message_template_pk' => [
                    'fieldName' => 'message_template_pk',
                    'scheme' => [
                        'extra' => 'auto_increment',
                        'type' => 'int(11)',
                        'dataType' => 'int',
                        'length' => '10,0',
                        'characterSet' => null,
                        'nullable' => false,
                        'default' => null,
                        'comment' => '',
                    ],
                    'options' => [
                        'name' => 'message_template_pk',
                        'type' => 'number',
                    ],
                ],
                'message_template_name' => [
                    'fieldName' => 'message_template_name',
                    'scheme' => [
                        'extra' => '',
                        'type' => 'varchar(255)',
                        'dataType' => 'varchar',
                        'length' => '255',
                        'characterSet' => 'utf8',
                        'nullable' => false,
                        'default' => 'Название шаблона',
                        'comment' => '',
                    ],
                    'options' => [
                        'name' => 'message_template_name',
                        'type' => 'text',
                        'validators' => [
                            'Ice:Length_Max' => 255,
                            0 => 'Ice:Not_Null',
                        ],
                    ],
                ],
                'subject' => [
                    'fieldName' => 'subject',
                    'scheme' => [
                        'extra' => '',
                        'type' => 'varchar(255)',
                        'dataType' => 'varchar',
                        'length' => '255',
                        'characterSet' => 'utf8',
                        'nullable' => false,
                        'default' => 'Тема письма',
                        'comment' => '',
                    ],
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
                    'fieldName' => 'body',
                    'scheme' => [
                        'extra' => '',
                        'type' => 'text',
                        'dataType' => 'text',
                        'length' => '65535',
                        'characterSet' => 'utf8',
                        'nullable' => false,
                        'default' => 'Тело письма',
                        'comment' => '',
                    ],
                    'options' => [
                        'name' => 'body',
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
                        1 => 'message_template_pk',
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
            'revision' => '08051922_ss1',
            'moduleAlias' => 'Ice',
            'modelClass' => 'Ice\Model\Message_Template',
        ];
    }
}