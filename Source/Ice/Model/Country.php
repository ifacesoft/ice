<?php namespace Ebs\Model;

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
class Ice_Country extends Model
{
    protected static function config()
    {
        return [
		    'dataSourceKey' => 'Ice\\Data\\Source\\Mysqli/default.test',
		    'scheme' => [
		        'tableName' => 'ice_country',
		        'engine' => 'InnoDB',
		        'charset' => 'utf8_general_ci',
		        'comment' => '',
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
		            'Ice\\Core\\Widget_Form' => 'Field_Number',
		            'Ice\\Core\\Validator' => [],
		            'Ice\\Core\\Widget_Data' => 'text',
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
		            'Ice\\Core\\Widget_Form' => 'Field_Text',
		            'Ice\\Core\\Validator' => [],
		            'Ice\\Core\\Widget_Data' => 'text',
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
		            'Ebs\\Model\\Ice_City' => 'country__fk',
		        ],
		        'manyToMany' => [],
		    ],
		    'revision' => '09251203_tlf',
		];
    }
}