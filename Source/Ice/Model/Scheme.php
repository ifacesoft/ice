<?php namespace Ice\Model;

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
 * @see Ice\Core\Model
 *
 * @package Ebs\Model
 */
class Scheme extends Model
{
    protected static function config()
    {
        return [
		    'dataSourceKey' => 'Ice\Data\Source\Mysqli/default.binardi',
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
		            'fieldName' => 'table_name',
		            'Ice\Widget\Model_Form' => 'text',
		            'Ice\Core\Validator' => [
		                0 => 'Ice:Not_Null',
		            ],
		            'Ice\Widget\Model_Table' => 'text',
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
		            'fieldName' => 'revision',
		            'Ice\Widget\Model_Form' => 'text',
		            'Ice\Core\Validator' => [
		                0 => 'Ice:Not_Null',
		            ],
		            'Ice\Widget\Model_Table' => 'text',
		        ],
		        'table__json' => [
		            'scheme' => [
		                'extra' => '',
		                'type' => 'varchar(1023)',
		                'dataType' => 'varchar',
		                'length' => '1023',
		                'characterSet' => 'utf8',
		                'nullable' => false,
		                'default' => '[]',
		                'comment' => '',
		            ],
		            'fieldName' => 'table__json',
		            'Ice\Widget\Model_Form' => 'text',
		            'Ice\Core\Validator' => [
		                0 => 'Ice:Not_Null',
		            ],
		            'Ice\Widget\Model_Table' => 'text',
		        ],
		        'columns__json' => [
		            'scheme' => [
		                'extra' => '',
		                'type' => 'text',
		                'dataType' => 'text',
		                'length' => '65535',
		                'characterSet' => 'utf8',
		                'nullable' => false,
		                'default' => '[]',
		                'comment' => '',
		            ],
		            'fieldName' => 'columns__json',
		            'Ice\Widget\Model_Form' => 'textarea',
		            'Ice\Core\Validator' => [
		                0 => 'Ice:Not_Null',
		            ],
		            'Ice\Widget\Model_Table' => 'text',
		        ],
		        'references__json' => [
		            'scheme' => [
		                'extra' => '',
		                'type' => 'varchar(1023)',
		                'dataType' => 'varchar',
		                'length' => '1023',
		                'characterSet' => 'utf8',
		                'nullable' => false,
		                'default' => '[]',
		                'comment' => '',
		            ],
		            'fieldName' => 'references__json',
		            'Ice\Widget\Model_Form' => 'text',
		            'Ice\Core\Validator' => [
		                0 => 'Ice:Not_Null',
		            ],
		            'Ice\Widget\Model_Table' => 'text',
		        ],
		        'indexes__json' => [
		            'scheme' => [
		                'extra' => '',
		                'type' => 'varchar(1023)',
		                'dataType' => 'varchar',
		                'length' => '1023',
		                'characterSet' => 'utf8',
		                'nullable' => false,
		                'default' => '[]',
		                'comment' => '',
		            ],
		            'fieldName' => 'indexes__json',
		            'Ice\Widget\Model_Form' => 'text',
		            'Ice\Core\Validator' => [
		                0 => 'Ice:Not_Null',
		            ],
		            'Ice\Widget\Model_Table' => 'text',
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
		    'revision' => '10130929_5rf',
		];
    }
}