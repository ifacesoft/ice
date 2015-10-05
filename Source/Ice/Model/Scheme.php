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
		            'Ice\Widget\Form_Model' => 'Field_Text',
		            'Ice\Core\Validator' => [
		                0 => 'Ice:Not_Null',
		            ],
		            'Ice\Widget\Table_Model' => 'text',
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
		            'Ice\Widget\Form_Model' => 'Field_Text',
		            'Ice\Core\Validator' => [
		                0 => 'Ice:Not_Null',
		            ],
		            'Ice\Widget\Table_Model' => 'text',
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
		            'Ice\Widget\Form_Model' => 'Field_Text',
		            'Ice\Core\Validator' => [
		                0 => 'Ice:Not_Null',
		            ],
		            'Ice\Widget\Table_Model' => 'text',
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
		            'Ice\Widget\Form_Model' => 'Field_Textarea',
		            'Ice\Core\Validator' => [
		                0 => 'Ice:Not_Null',
		            ],
		            'Ice\Widget\Table_Model' => 'text',
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
		            'Ice\Widget\Form_Model' => 'Field_Text',
		            'Ice\Core\Validator' => [
		                0 => 'Ice:Not_Null',
		            ],
		            'Ice\Widget\Table_Model' => 'text',
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
		            'Ice\Widget\Form_Model' => 'Field_Text',
		            'Ice\Core\Validator' => [
		                0 => 'Ice:Not_Null',
		            ],
		            'Ice\Widget\Table_Model' => 'text',
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
		    'revision' => '10051437_lop',
		];
    }
}