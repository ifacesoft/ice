<?php

namespace Ice\Model;

use Ice\Core\Model;

/**
 * Class Scheme
 *
 * @property mixed table_name
 * @property mixed revision
 * @property mixed table__json
 * @property mixed columns__json
 * @property mixed references__json
 * @property mixed indexes__json
 *
 * @see \Ice\Core\Model
 *
 * @package Ice\Model
 */
class Scheme extends Model
{
    protected static function config()
    {
        return [
            'dataSourceKey' => 'Ice\DataSource\Mysqli/default.test',
            'scheme' => [
                'tableName' => 'ice_scheme',
                'engine' => 'InnoDB',
                'charset' => 'utf8_general_ci',
                'comment' => '',
            ],
            'columns' => [
                'table_name' => [
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
                        'name' => 'table_name',
                        'type' => 'text',
                        'validators' => [
                            'Ice:Length_Max' => 255,
                            0 => 'Ice:Not_Null',
                        ],
                    ],
                    'fieldName' => 'table_name',
                ],
                'revision' => [
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
                        'name' => 'revision',
                        'type' => 'text',
                        'validators' => [
                            'Ice:Length_Max' => 12,
                            0 => 'Ice:Not_Null',
                        ],
                    ],
                    'fieldName' => 'revision',
                ],
                'table__json' => [
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
                        'name' => 'table__json',
                        'type' => 'text',
                        'validators' => [
                            'Ice:Length_Max' => 1023,
                            0 => 'Ice:Not_Null',
                        ],
                    ],
                    'fieldName' => 'table__json',
                ],
                'columns__json' => [
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
                        'name' => 'columns__json',
                        'type' => 'textarea',
                        'validators' => [
                            'Ice:Length_Max' => 65535,
                            0 => 'Ice:Not_Null',
                        ],
                    ],
                    'fieldName' => 'columns__json',
                ],
                'references__json' => [
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
                        'name' => 'references__json',
                        'type' => 'text',
                        'validators' => [
                            'Ice:Length_Max' => 1023,
                            0 => 'Ice:Not_Null',
                        ],
                    ],
                    'fieldName' => 'references__json',
                ],
                'indexes__json' => [
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
                        'name' => 'indexes__json',
                        'type' => 'text',
                        'validators' => [
                            'Ice:Length_Max' => 1023,
                            0 => 'Ice:Not_Null',
                        ],
                    ],
                    'fieldName' => 'indexes__json',
                ],
            ],
            'indexes' => [
                'PRIMARY KEY' => [
                    'PRIMARY' => [],
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
            'revision' => '05121856_nvf',
            'moduleAlias' => 'Ice',
            'modelClass' => 'Ice\Model\Scheme',
        ];
    }
}