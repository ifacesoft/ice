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
		    'dataSourceKey' => 'Ice\Data\Source\Mysqli/default.www',
		    'scheme' => [
		        'tableName' => 'ice_user_role_link',
		        'engine' => 'InnoDB',
		        'charset' => 'utf8_general_ci',
		        'comment' => '',
		    ],
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
		            'fieldName' => 'user__fk',
		            'Ice\Core\Widget_Form' => 'Number',
		            'Ice\Core\Validator' => [],
		            'Ice\Core\Widget_Data' => 'text',
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
		            'fieldName' => 'role__fk',
		            'Ice\Core\Widget_Form' => 'Number',
		            'Ice\Core\Validator' => [],
		            'Ice\Core\Widget_Data' => 'text',
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
		            'PRIMARY' => [
		                'fk_ice_user_role_link_ice_user' => 'user__fk',
		            ],
		        ],
		        'UNIQUE' => [],
		    ],
		    'references' => [
		        'ice_role' => [
		            'constraintName' => 'fk_ice_user_role_link_ice_role',
		            'onUpdate' => 'NO ACTION',
		            'onDelete' => 'NO ACTION',
		        ],
		        'ice_user' => [
		            'constraintName' => 'fk_ice_user_role_link_ice_user',
		            'onUpdate' => 'NO ACTION',
		            'onDelete' => 'NO ACTION',
		        ],
		    ],
		    'relations' => [
		        'oneToMany' => [
		            'ice_role' => 'role__fk',
		            'ice_user' => 'user__fk',
		        ],
		        'manyToOne' => [],
		        'manyToMany' => [],
		    ],
		    'revision' => '05072044_axg',
		];
    }
}