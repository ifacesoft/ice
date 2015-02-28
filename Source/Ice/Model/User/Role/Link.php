<?php namespace Ice\Model;

use Ice\Core\Model;

/**
 * Class User_Role_Link
 *
 * @property mixed user__fk
 * @property mixed role__fk
 *
 * @see Ice\Core\Model
 *
 * @package Ice\Model
 */
class User_Role_Link extends Model
{
    protected static function config()
    {
        return [
		    'revision' => '02281701_Yk',
		    'dataSourceKey' => null,
		    'scheme' => [
		        'tableName' => 'ice_user_role_link',
		        'engine' => 'InnoDB',
		        'charset' => 'utf8_general_ci',
		        'comment' => '',
		    ],
		    'schemeHash' => 3357336535,
		    'columns' => [
		        'user__fk' => [
		            'scheme' => [
		                'extra' => '',
		                'type' => 'bigint(20)',
		                'dataType' => 'bigint',
		                'length' => '19,0',
		                'characterSet' => null,
		                'nullable' => false,
		                'default' => null,
		                'comment' => '',
		            ],
		            'schemeHash' => 2126507909,
		            'fieldName' => 'user__fk',
		            'Ice\\Core\\Form' => 'Number',
		            'Ice\\Core\\Validator' => [],
		            'Ice\\Core\\Data' => 'text',
		        ],
		        'role__fk' => [
		            'scheme' => [
		                'extra' => '',
		                'type' => 'bigint(20)',
		                'dataType' => 'bigint',
		                'length' => '19,0',
		                'characterSet' => null,
		                'nullable' => false,
		                'default' => null,
		                'comment' => '',
		            ],
		            'schemeHash' => 2126507909,
		            'fieldName' => 'role__fk',
		            'Ice\\Core\\Form' => 'Number',
		            'Ice\\Core\\Validator' => [],
		            'Ice\\Core\\Data' => 'text',
		        ],
		    ],
		    'indexes' => [
		        'PRIMARY KEY' => [
		            'PRIMARY' => [
		                1 => 'user__fk',
		                2 => 'role__fk',
		            ],
		        ],
		        'FOREIGN KEY' => [
		            'fk_ice_user_role_link_ice_role' => [
		                'fk_ice_user_role_link_ice_role' => 'role__fk',
		            ],
		            'user__fk' => [
		                'fk_ice_user_role_link_ice_user' => 'user__fk',
		            ],
		        ],
		        'UNIQUE' => [],
		    ],
		    'indexesHash' => 2881720663,
		];
    }
}