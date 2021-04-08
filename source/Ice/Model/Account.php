<?php

namespace Ice\Model;

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
		    'dataSourceKey' => 'Ice\Data\Source\Mysqli/default.test',
		    'scheme' => [
		        'tableName' => 'ice_account',
		        'engine' => 'InnoDB',
		        'charset' => 'utf8_general_ci',
		        'comment' => '',
		    ],
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
		            'fieldName' => 'account_pk',
		            'Ice\Core\Widget_Form' => 'Number',
		            'Ice\Core\Validator' => [],
		            'Ice\Core\Widget_Data' => 'text',
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
		            'fieldName' => 'user__fk',
		            'Ice\Core\Widget_Form' => 'Number',
		            'Ice\Core\Validator' => [],
		            'Ice\Core\Widget_Data' => 'text',
		        ],
		        'login' => [
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
		            'fieldName' => 'login',
		            'Ice\Core\Widget_Form' => 'Text',
		            'Ice\Core\Validator' => [
		                'Ice:Length_Max' => 255,
		            ],
		            'Ice\Core\Widget_Data' => 'text',
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
		            'fieldName' => 'password',
		            'Ice\Core\Widget_Form' => 'Text',
		            'Ice\Core\Validator' => [
		                'Ice:Length_Max' => 255,
		            ],
		            'Ice\Core\Widget_Data' => 'text',
		        ],
		        'account_active' => [
		            'scheme' => [
		                'extra' => '',
		                'type' => 'tinyint(4)',
		                'dataType' => 'tinyint',
		                'length' => '3,0',
		                'characterSet' => null,
		                'nullable' => false,
		                'default' => '1',
		                'comment' => '',
		            ],
		            'fieldName' => 'account_active',
		            'Ice\Core\Widget_Form' => 'Checkbox',
		            'Ice\Core\Validator' => [
		                0 => 'Ice:Not_Null',
		            ],
		            'Ice\Core\Widget_Data' => 'text',
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
		    'references' => [
		        'ice_user' => [
		            'constraintName' => 'fk_ice_account_ice_user',
		            'onUpdate' => 'NO ACTION',
		            'onDelete' => 'NO ACTION',
		        ],
		    ],
		    'relations' => [
		        'oneToMany' => [
		            'ice_user' => 'user__fk',
		        ],
		        'manyToOne' => [],
		        'manyToMany' => [],
		    ],
		    'revision' => '05102017_ame',
		];
    }
}