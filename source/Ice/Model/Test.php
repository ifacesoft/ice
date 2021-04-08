<?php

namespace Ice\Model;

use Ice\Core\Model;

/**
 * Class test
 *
 * @property mixed test_pk
 * @property mixed test_name
 * @property mixed name2
 *
 * @see \Ice\Core\Model
 *
 * @package Ice\Model
 */
class Test extends Model
{
    protected static function config()
    {
        return [
            'dataSourceKey' => 'Ice\DataSource\Mysqli/default.test',
            'scheme' => [
                'tableName' => 'ice_test',
                'engine' => 'InnoDB',
                'charset' => 'utf8_general_ci',
                'comment' => '',
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
                    'options' => [
                        'name' => 'test_pk',
                        'type' => 'number',
                    ],
                    'fieldName' => 'test_pk',
                ],
                'test_name' => [
                    'scheme' => [
                        'extra' => '',
                        'type' => 'varchar(52)',
                        'dataType' => 'varchar',
                        'length' => '52',
                        'characterSet' => 'utf8',
                        'nullable' => false,
                        'default' => null,
                        'comment' => '',
                    ],
                    'options' => [
                        'name' => 'test_name',
                        'type' => 'text',
                        'validators' => [
                            'Ice:Length_Max' => 52,
                            0 => 'Ice:Not_Null',
                        ],
                    ],
                    'fieldName' => 'test_name',
                ],
                'name2' => [
                    'scheme' => [
                        'extra' => '',
                        'type' => 'varchar(65)',
                        'dataType' => 'varchar',
                        'length' => '65',
                        'characterSet' => 'utf8',
                        'nullable' => true,
                        'default' => null,
                        'comment' => '',
                    ],
                    'options' => [
                        'name' => 'name2',
                        'type' => 'text',
                        'validators' => [
                            'Ice:Length_Max' => 65,
                        ],
                    ],
                    'fieldName' => 'name2',
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
            'revision' => '05121856_lp6',
            'moduleAlias' => 'Ice',
        ];
    }
}