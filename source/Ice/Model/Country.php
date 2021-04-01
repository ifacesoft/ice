<?php

namespace Ice\Model;

use Ice\Core\Model;

/**
 * Class Ice_Country
 *
 * @property mixed country_pk
 * @property mixed country_name
 *
 * @see \Ice\Core\Model
 *
 * @package Ice\Model
 */
class Country extends Model
{
    protected static function config()
    {
        return [
            'dataSourceKey' => 'Ice\DataSource\Mysqli/default.test',
            'scheme' => [
                'tableName' => 'ice_country',
                'engine' => 'InnoDB',
                'charset' => 'utf8_general_ci',
                'comment' => 'Страны',
            ],
            'columns' => [
                'country_pk' => [
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
                    'fieldName' => 'country_pk',
                    'options' => [
                        'name' => 'country_pk',
                        'type' => 'number',
                    ],
                ],
                'country_name' => [
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
                    'fieldName' => 'country_name',
                    'options' => [
                        'name' => 'country_name',
                        'type' => 'text',
                        'validators' => [
                            'Ice:Length_Max' => 255,
                        ],
                    ],
                ],
            ],
            'indexes' => [
                'PRIMARY KEY' => [
                    'PRIMARY' => [
                        1 => 'country_pk',
                    ],
                ],
                'FOREIGN KEY' => [],
                'UNIQUE' => [
                    'country_name' => [
                        1 => 'country_name',
                    ],
                ],
            ],
            'references' => [],
            'relations' => [
                'oneToMany' => [],
                'manyToOne' => [
                    'Ice\Model\City' => 'country__fk',
                ],
                'manyToMany' => [],
            ],
            'revision' => '09251203_tlf',
            'moduleAlias' => 'Ice',
            'modelClass' => 'Ice\Model\Country',
        ];
    }
}