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
		    'revision' => '03071316_L4',
		    'dataSourceKey' => 'Ice\\Data\\Source\\Mysqli/default.test',
		    'scheme' => [
		        'tableName' => 'ice_test',
		        'engine' => 'InnoDB',
		        'charset' => 'utf8_general_ci',
		        'comment' => 'dsdsddsdddsdsd',
		    ],
		    'schemeHash' => 3201865154,
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
		            'Ice\\Core\\Form' => 'Text',
		            'Ice\\Core\\Validator' => [
		                'Ice:Length_Max' => 52,
		                0 => 'Ice:Not_Null',
		            ],
		            'Ice\\Core\\Data' => 'text',
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
		            'Ice\\Core\\Form' => 'Text',
		            'Ice\\Core\\Validator' => [
		                'Ice:Length_Max' => 65,
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