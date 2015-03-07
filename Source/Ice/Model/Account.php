<?php namespace Ice\Model;

use Ice\Core\Model;

/**
 * Class Account
 *
 * @property mixed account_pk
 * @property mixed user__fk
 * @property mixed login
 * @property mixed password
 * @property mixed account_active
 *
 * @see Ice\Core\Model
 *
 * @package Ice\Model
 */
class Account extends Model
{
    protected static function config()
    {
        return [
		    'revision' => '03071316_6O',
		    'dataSourceKey' => 'Ice\\Data\\Source\\Mysqli/default.test',
		    'scheme' => [
		        'tableName' => 'ice_account',
		        'engine' => 'InnoDB',
		        'charset' => 'utf8_general_ci',
		        'comment' => '',
		    ],
		    'schemeHash' => 3866377868,
		    'columns' => [
		        'account_pk' => [
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
		            'fieldName' => 'account_pk',
		            'Ice\\Core\\Form' => 'Number',
		            'Ice\\Core\\Validator' => [],
		            'Ice\\Core\\Data' => 'text',
		        ],
		        'user__fk' => [
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
		            'schemeHash' => 3262077293,
		            'fieldName' => 'user__fk',
		            'Ice\\Core\\Form' => 'Number',
		            'Ice\\Core\\Validator' => [],
		            'Ice\\Core\\Data' => 'text',
		        ],
		        'login' => [
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
		            'schemeHash' => 1922669372,
		            'fieldName' => 'login',
		            'Ice\\Core\\Form' => 'Text',
		            'Ice\\Core\\Validator' => [
		                'Ice:Length_Max' => 255,
		                0 => 'Ice:Not_Null',
		            ],
		            'Ice\\Core\\Data' => 'text',
		        ],
		        'password' => [
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
		            'schemeHash' => 1375787174,
		            'fieldName' => 'password',
		            'Ice\\Core\\Form' => 'Text',
		            'Ice\\Core\\Validator' => [
		                'Ice:Length_Max' => 255,
		            ],
		            'Ice\\Core\\Data' => 'text',
		        ],
		        'account_active' => [
		            'scheme' => [
		                'extra' => '',
		                'type' => 'tinyint(1)',
		                'dataType' => 'tinyint',
		                'length' => '3,0',
		                'characterSet' => null,
		                'nullable' => false,
		                'default' => '1',
		                'comment' => '',
		            ],
		            'schemeHash' => 725562207,
		            'fieldName' => 'account_active',
		            'Ice\\Core\\Form' => 'Checkbox',
		            'Ice\\Core\\Validator' => [
		                0 => 'Ice:Not_Null',
		            ],
		            'Ice\\Core\\Data' => 'text',
		        ],
		    ],
		    'indexes' => [
		        'PRIMARY KEY' => [
		            'PRIMARY' => [
		                1 => 'account_pk',
		            ],
		        ],
		        'FOREIGN KEY' => [
		            'fk_ice_account_ice_user' => [
		                'fk_ice_account_ice_user' => 'user__fk',
		            ],
		        ],
		        'UNIQUE' => [],
		    ],
		    'indexesHash' => 3986220866,
		];
    }
}