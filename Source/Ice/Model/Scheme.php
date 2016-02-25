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
		    'dataSourceKey' => 'Ice\DataSource\Mysqli/default.ebs_text',
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
		            'Ice\Widget\Model_Form' => [
		                'type' => 'text',
		            ],
		            'Ice\Core\Validator' => [
		                'Ice:Length_Max' => 255,
		                0 => 'Ice:Not_Null',
		            ],
		            'Ice\Widget\Model_Table' => [
		                'type' => 'span',
		            ],
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
		            'Ice\Widget\Model_Form' => [
		                'type' => 'text',
		            ],
		            'Ice\Core\Validator' => [
		                'Ice:Length_Max' => 12,
		                0 => 'Ice:Not_Null',
		            ],
		            'Ice\Widget\Model_Table' => [
		                'type' => 'span',
		            ],
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
		            'Ice\Widget\Model_Form' => [
		                'type' => 'text',
		            ],
		            'Ice\Core\Validator' => [
		                'Ice:Length_Max' => 1023,
		                0 => 'Ice:Not_Null',
		            ],
		            'Ice\Widget\Model_Table' => [
		                'type' => 'span',
		            ],
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
		            'Ice\Widget\Model_Form' => [
		                'type' => 'textarea',
		            ],
		            'Ice\Core\Validator' => [
		                'Ice:Length_Max' => 65535,
		                0 => 'Ice:Not_Null',
		            ],
		            'Ice\Widget\Model_Table' => [
		                'type' => 'span',
		            ],
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
		            'Ice\Widget\Model_Form' => [
		                'type' => 'text',
		            ],
		            'Ice\Core\Validator' => [
		                'Ice:Length_Max' => 1023,
		                0 => 'Ice:Not_Null',
		            ],
		            'Ice\Widget\Model_Table' => [
		                'type' => 'span',
		            ],
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
		            'Ice\Widget\Model_Form' => [
		                'type' => 'text',
		            ],
		            'Ice\Core\Validator' => [
		                'Ice:Length_Max' => 1023,
		                0 => 'Ice:Not_Null',
		            ],
		            'Ice\Widget\Model_Table' => [
		                'type' => 'span',
		            ],
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
		    'revision' => '02251418_l0a',
		];
    }
}