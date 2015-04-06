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
		    'dataSourceKey' => 'Ice\Data\Source\Mysqli/default.test',
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
		            'schemeHash' => 708064701,
		            'fieldName' => 'test_pk',
		            'Ice\Core\Ui_Form' => 'Number',
		            'Ice\Core\Validator' => [],
		            'Ice\Core\Ui_Data' => 'text',
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
		            'schemeHash' => 336823333,
		            'fieldName' => 'test_name',
		            'Ice\Core\Ui_Form' => 'Text',
		            'Ice\Core\Validator' => [
		                'Ice:Length_Max' => 52,
		                0 => 'Ice:Not_Null',
		            ],
		            'Ice\Core\Ui_Data' => 'text',
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
		            'schemeHash' => 2521633375,
		            'fieldName' => 'name2',
		            'Ice\Core\Ui_Form' => 'Text',
		            'Ice\Core\Validator' => [
		                'Ice:Length_Max' => 65,
		            ],
		            'Ice\Core\Ui_Data' => 'text',
		        ],
		    ],
		    'oneToMany' => [],
		    'manyToOne' => [],
		    'manyToMany' => [],
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
		    'revision' => '04061152_si',
		    'schemeHash' => 3368682505,
		    'indexesHash' => 3780610860,
		    'referencesHash' => 223132457,
		    'oneToManyHash' => 223132457,
		    'manyToOneHash' => 223132457,
		    'manyToManyHash' => 223132457,
		];
    }
}