<?php

namespace Ice\Model;

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
		    'dataSourceKey' => 'Ice\Data\Source\Mysqli/default.test',
		    'scheme' => [
		        'tableName' => 'ice_user_role_link',
		        'engine' => 'InnoDB',
		        'charset' => 'utf8_general_ci',
		        'comment' => '',
		    ],
		    'columns' => [
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
		            'Ice\Core\Validator' => [
		                0 => 'Ice:Not_Null',
		            ],
		            'Ice\Core\Widget_Data' => 'text',
		        ],
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
		            'Ice\Core\Validator' => [
		                0 => 'Ice:Not_Null',
		            ],
		            'Ice\Core\Widget_Data' => 'text',
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
		    'revision' => '05151619_n1s',
		];
    }
}