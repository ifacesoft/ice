<?php namespace Ice\Model;

use Ice\Core\Model;

/**
 * Class Test
 *
 * @property mixed test_pk
 * @property mixed test_name
 * @property mixed name2
 *
 * @see Ice\Core\Model
 *
 * @package Ice\Model
 */
class Test extends Model
{
    protected static function config()
    {
        return [
		    'dataSourceKey' => 'Ice\Data\Source\Mysqli/default.www',
		    'scheme' => [
		        'tableName' => 'ice_test',
		        'engine' => 'InnoDB',
		        'charset' => 'utf8_general_ci',
		        'comment' => '',
		    ],
		    'columns' => [
		        'id' => [
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
		            'fieldName' => 'ice_test_pk',
		            'Ice\Core\Widget_Form' => 'Number',
		            'Ice\Core\Validator' => [],
		            'Ice\Core\Widget_Data' => 'text',
		        ],
		        'test_name' => [
		            'scheme' => [
		                'extra' => '',
		                'type' => 'varchar(52)',
		                'dataType' => 'varchar',
		                'length' => '52',
		                'characterSet' => 'utf8',
		                'nullable' => false,
		                'default' => null,
		                'comment' => '',
		            ],
		            'fieldName' => 'test_name',
		            'Ice\Core\Widget_Form' => 'Text',
		            'Ice\Core\Validator' => [
		                'Ice:Length_Max' => 52,
		                0 => 'Ice:Not_Null',
		            ],
		            'Ice\Core\Widget_Data' => 'text',
		        ],
		        'name2' => [
		            'scheme' => [
		                'extra' => '',
		                'type' => 'varchar(65)',
		                'dataType' => 'varchar',
		                'length' => '65',
		                'characterSet' => 'utf8',
		                'nullable' => true,
		                'default' => null,
		                'comment' => '',
		            ],
		            'fieldName' => 'name2',
		            'Ice\Core\Widget_Form' => 'Text',
		            'Ice\Core\Validator' => [
		                'Ice:Length_Max' => 65,
		            ],
		            'Ice\Core\Widget_Data' => 'text',
		        ],
		    ],
		    'indexes' => [
		        'PRIMARY KEY' => [
		            'PRIMARY' => [
		                1 => 'id',
		            ],
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
		    'revision' => '05041414_8st',
		];
    }
}