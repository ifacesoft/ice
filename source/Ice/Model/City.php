<?php

namespace Ice\Model;

use Ice\Core\Model;

/**
 * Class City
 *
 * @property mixed city_pk
 * @property mixed city_name
 * @property mixed city_short
 * @property mixed country__fk
 *
 * @see \Ice\Core\Model
 *
 * @package Ice\Model
 */
class City extends Model
{
    protected static function config()
    {
        return [
            'dataSourceKey' => 'Ice\DataSource\Mysqli/default.test',
            'scheme' => [
                'tableName' => 'ice_city',
                'engine' => 'InnoDB',
                'charset' => 'utf8_general_ci',
                'comment' => 'Города',
            ],
            'roles' => [
                0 => 'ROLE_ICE_ADMIN',
            ],
            'createRoles' => [
                0 => 'ROLE_ICE_ADMIN',
            ],
            'joins' => [
                0 => 'Ice\Model\Country',
            ],
            'relations' => [
                'oneToMany' => [
                    'Ice\Model\Country' => 'country__fk',
                ],
                'manyToOne' => [],
                'manyToMany' => [],
            ],
            'columns' => [
                'city_pk' => [
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
                    'fieldName' => 'city_pk',
                    'options' => [
                        'name' => 'city_pk',
                        'type' => 'number',
                    ],
                ],
                'city_name' => [
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
                    'fieldName' => 'city_name',
                    'options' => [
                        'name' => 'city_name',
                        'type' => 'text',
                        'validators' => [
                            'Ice:Length_Max' => 255,
                        ],
                        'show' => [
                            'valueKey' => true,
                            'route' => [
                                'name' => 'adm_database_row',
                                'params' => [
                                    'pk' => 'city_pk',
                                    0 => 'schemeName',
                                    1 => 'tableName',
                                ],
                            ],
                            'target' => '_blank',
                        ],
                        'add' => [
                            'required' => true,
                        ],
                        'edit' => [
                            'required' => true,
                        ],
                    ],
                ],
                'city_short' => [
                    'scheme' => [
                        'extra' => '',
                        'type' => 'varchar(32)',
                        'dataType' => 'varchar',
                        'length' => '32',
                        'characterSet' => 'utf8',
                        'nullable' => true,
                        'default' => null,
                        'comment' => '',
                    ],
                    'fieldName' => 'city_short',
                    'options' => [
                        'name' => 'city_short',
                        'type' => 'text',
                        'validators' => [
                            'Ice:Length_Max' => 32,
                        ],
                        'add' => [
                            'required' => true,
                        ],
                        'edit' => [
                            'required' => true,
                        ],
                    ],
                ],
                'country__fk' => [
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
                    'fieldName' => 'country__fk',
                    'options' => [
                        'name' => 'country__fk',
                        'type' => 'oneToMany',
                        'show' => [
                            'valueKey' => 'country_name',
                        ],
                        'filter' => [
                            'comparison' => '=',
                        ],
                        'itemModel' => 'Ice\Model\Country',
                        'itemKey' => 'country_pk',
                        'itemTitle' => 'country_name',
                    ],
                ],
            ],
            'indexes' => [
                'PRIMARY KEY' => [
                    'PRIMARY' => [
                        1 => 'city_pk',
                    ],
                ],
                'FOREIGN KEY' => [
                    'fk_ice_city_ice_country' => [
                        'fk_ice_city_ice_country' => 'country__fk',
                    ],
                ],
                'UNIQUE' => [
                    'city_name' => [
                        1 => 'city_name',
                    ],
                ],
            ],
            'references' => [
                'ice_country' => [
                    'constraintName' => 'fk_ice_city_ice_country',
                    'onUpdate' => 'NO ACTION',
                    'onDelete' => 'NO ACTION',
                ],
            ],
            'revision' => '09251203_xw0',
            'moduleAlias' => 'Ice',
            'modelClass' => 'Ice\Model\City',
        ];
    }
}