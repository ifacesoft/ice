<?php namespace Ice\Model;

use Ice\Core\Model;

/**
 * Class City
 *
 * @property mixed city_pk
 * @property mixed city_name
 * @property mixed city_short
 * @property mixed country__fk
 *
 * @see Ice\Core\Model
 *
 * @package Ebs\Model
 */
class City extends Model
{
    protected static function config()
    {
        return [
            'dataSourceKey' => 'Ice\DataSource\Mysqli/default.ebs',
            'scheme' => [
                'tableName' => 'ice_city',
                'engine' => 'InnoDB',
                'charset' => 'utf8_general_ci',
                'comment' => 'Города',
            ],
            'roles' => 'ROLE_LAN_STAFF',
            'createRoles' => 'ROLE_LAN_STAFF',
            'joins' => ['Ice\Model\Country'],
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
                        'show' => [
                            'roles' => 'ROLE_LAN_STAFF',
                        ],
                        'edit' => [
                            'roles' => 'ROLE_LAN_STAFF',
                            'readonly' => true,
                        ],
                        'filter' => [
                            'roles' => 'ROLE_LAN_STAFF',
                            'comparison' => '=',
                        ],
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
                                'name' => 'ice_admin_database_row',
                                'params' => [
                                    'pk' => 'city_pk',
                                    0 => 'schemeName',
                                    1 => 'tableName',
                                ],
                            ],
                        ],
                        'add' => [
                            'required' => true,
                        ],
                        'edit' => [
                            'required' => true,
                        ],
                        'roles' => 'ROLE_LAN_STAFF',
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
                        'roles' => 'ROLE_LAN_STAFF',
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
                        'roles' => 'ROLE_LAN_STAFF',
                        'show' => [
                            'valueKey' => 'country_name'
                        ],
                        'filter' => [
                            'comparison' => '=',
                        ],
                        'itemModel' => 'Ice\Model\Country',
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
            'relations' => [
                'oneToMany' => [
                    'Ice\Model\Country' => 'country__fk',
                ],
                'manyToOne' => [
                    'Ebs\Model\Journal' => 'city_id',
                    'Ebs\Model\Publisher' => 'city_id',
                    'Ebs\Model\Subscriber' => 'city_id',
                ],
                'manyToMany' => [
                    'Ebs\Model\Publisher' => [
                        0 => 'Ebs\Model\Journal',
                    ],
                    'Ebs\Model\Access_Type' => [
                        0 => 'Ebs\Model\Subscriber',
                    ],
                    'Ebs\Model\Subscriber' => [
                        0 => 'Ebs\Model\Subscriber',
                    ],
                    'Ebs\Model\Subscriber_Type' => [
                        0 => 'Ebs\Model\Subscriber',
                    ],
                ],
            ],
            'revision' => '09251203_xw0',
            'modelClass' => 'Ice\Model\City',
            'modelPath' => 'Ice/Model/City.php',
        ];
    }
}