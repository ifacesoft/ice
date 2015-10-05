<?php namespace Ice\Model;

use Ice\Core\Model;

/**
 * Class Ice_City
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
		    'dataSourceKey' => 'Ice\Data\Source\Mysqli/default.lan',
		    'scheme' => [
		        'tableName' => 'ice_city',
		        'engine' => 'InnoDB',
		        'charset' => 'utf8_general_ci',
		        'comment' => '',
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
		            'Ice\Widget\Form_Model' => 'Field_Number',
		            'Ice\Core\Validator' => [],
		            'Ice\Widget\Table_Model' => 'text',
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
		            'Ice\Widget\Form_Model' => 'Field_Text',
		            'Ice\Core\Validator' => [],
		            'Ice\Widget\Table_Model' => 'text',
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
		            'Ice\Widget\Form_Model' => 'Field_Text',
		            'Ice\Core\Validator' => [],
		            'Ice\Widget\Table_Model' => 'text',
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
		            'Ice\Widget\Form_Model' => 'Field_Number',
		            'Ice\Core\Validator' => [],
		            'Ice\Widget\Table_Model' => 'text',
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
		        'UNIQUE' => [],
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
		            'Ebs\Model\Company' => 'city_id',
		        ],
		        'manyToMany' => [],
		    ],
		    'revision' => '09251203_xw0',
		    'modelClass' => 'Ice\Model\City',
		    'modelPath' => 'Ice/Model/City.php',
		];
    }
}