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
		            'Ice\Widget\Model_Form' => [
		                'type' => 'number',
		                'roles' => [
		                    0 => 'ROLE_ICE_GUEST',
		                    1 => 'ROLE_ICE_USER',
		                ],
		            ],
		            'Ice\Core\Validator' => [],
		            'Ice\Widget\Model_Table' => [
		                'type' => 'span',
		                'roles' => [
		                    0 => 'ROLE_ICE_GUEST',
		                    1 => 'ROLE_ICE_USER',
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
		            'Ice\Widget\Model_Form' => [
		                'type' => 'text',
		                'roles' => [
		                    0 => 'ROLE_ICE_GUEST',
		                    1 => 'ROLE_ICE_USER',
		                ],
		            ],
		            'Ice\Core\Validator' => [
		                'Ice:Length_Max' => 255,
		            ],
		            'Ice\Widget\Model_Table' => [
		                'type' => 'span',
		                'roles' => [
		                    0 => 'ROLE_ICE_GUEST',
		                    1 => 'ROLE_ICE_USER',
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
		            'Ice\Widget\Model_Form' => [
		                'type' => 'text',
		                'roles' => [
		                    0 => 'ROLE_ICE_GUEST',
		                    1 => 'ROLE_ICE_USER',
		                ],
		            ],
		            'Ice\Core\Validator' => [
		                'Ice:Length_Max' => 32,
		            ],
		            'Ice\Widget\Model_Table' => [
		                'type' => 'span',
		                'roles' => [
		                    0 => 'ROLE_ICE_GUEST',
		                    1 => 'ROLE_ICE_USER',
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
		            'Ice\Widget\Model_Form' => [
		                'type' => 'number',
		                'roles' => [
		                    0 => 'ROLE_ICE_GUEST',
		                    1 => 'ROLE_ICE_USER',
		                ],
		            ],
		            'Ice\Core\Validator' => [],
		            'Ice\Widget\Model_Table' => [
		                'type' => 'span',
		                'roles' => [
		                    0 => 'ROLE_ICE_GUEST',
		                    1 => 'ROLE_ICE_USER',
		                ],
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