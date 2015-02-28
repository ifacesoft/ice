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
		    'revision' => '02281735_nX',
		    'dataSourceKey' => null,
		    'scheme' => [
		        'tableName' => 'ice_test',
		        'engine' => 'InnoDB',
		        'charset' => 'utf8_general_ci',
		        'comment' => 'dsdsd',
		    ],
		    'schemeHash' => 360356730,
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
		            'Ice\\Core\\Form' => 'Number',
		            'Ice\\Core\\Validator' => [],
		            'Ice\\Core\\Data' => 'text',
		        ],
		        'test_name' => [
		            'scheme' => [
		                'extra' => '',
		                'type' => 'varchar(50)',
		                'dataType' => 'varchar',
		                'length' => '50',
		                'characterSet' => 'utf8',
		                'nullable' => false,
		                'default' => null,
		                'comment' => '',
		            ],
		            'schemeHash' => 1203205037,
		            'fieldName' => 'test_name',
		            'Ice\\Core\\Form' => 'Text',
		            'Ice\\Core\\Validator' => [
		                'Ice:Length_Max' => 50,
		                0 => 'Ice:Not_Null',
		            ],
		            'Ice\\Core\\Data' => 'text',
		        ],
		        'name2' => [
		            'scheme' => [
		                'extra' => '',
		                'type' => 'varchar(55)',
		                'dataType' => 'varchar',
		                'length' => '55',
		                'characterSet' => 'utf8',
		                'nullable' => true,
		                'default' => null,
		                'comment' => '',
		            ],
		            'schemeHash' => 3963330067,
		            'fieldName' => 'name2',
		            'Ice\\Core\\Form' => 'Text',
		            'Ice\\Core\\Validator' => [
		                'Ice:Length_Max' => 55,
		            ],
		            'Ice\\Core\\Data' => 'text',
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
		    'indexesHash' => 3780610860,
		];
    }
}