<?php namespace Ice\Model;

use Ice\Core\Model;

/**
 * Class Ice_Country
 *
 * @property mixed country_pk
 * @property mixed country_name
 *
 * @see Ice\Core\Model
 *
 * @package Ebs\Model
 */
class Country extends Model
{
    protected static function config()
    {
        return [
		    'dataSourceKey' => 'Ice\Data\Source\Mysqli/default.test',
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
		                'comment' => 'ID Страны',
		            ],
		            'fieldName' => 'country_pk',
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
		        'country_name' => [
		            'scheme' => [
		                'extra' => '',
		                'type' => 'varchar(255)',
		                'dataType' => 'varchar',
		                'length' => '255',
		                'characterSet' => 'utf8',
		                'nullable' => true,
		                'default' => null,
		                'comment' => 'Название страны',
		            ],
		            'fieldName' => 'country_name',
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
		    ],
		    'indexes' => [
		        'PRIMARY KEY' => [
		            'PRIMARY' => [
		                1 => 'country_pk',
		            ],
		        ],
		        'FOREIGN KEY' => [],
		        'UNIQUE' => [],
		    ],
		    'references' => [],
		    'relations' => [
		        'oneToMany' => [],
		        'manyToOne' => [
		            'Ebs\Model\Ice_City' => 'country__fk',
		        ],
		        'manyToMany' => [],
		    ],
		    'revision' => '09251203_tlf',
		    'modelClass' => 'Ice\Model\Country',
		    'modelPath' => 'Ice/Model/Country.php',
		];
    }
}