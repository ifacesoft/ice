<?php namespace Ebs\Model;

use Ice\Core\Model;

/**
 * Class Ice_Token
 *
 * @property mixed ice_token_pk
 * @property mixed token
 * @property mixed token_expired
 *
 * @see Ice\Core\Model
 *
 * @package Ebs\Model
 */
class Token extends Model
{
    protected static function config()
    {
        return [
		    'dataSourceKey' => 'Ice\\Data\\Source\\Mysqli/default.ebs',
		    'scheme' => [
		        'tableName' => 'ice_token',
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
		            'fieldName' => 'ice_token_pk',
		            'Ice\\Widget\\Model_Form' => [
		                'type' => 'number',
		                'roles' => [
		                    0 => 'ROLE_ICE_GUEST',
		                    1 => 'ROLE_ICE_USER',
		                ],
		            ],
		            'Ice\\Core\\Validator' => [],
		            'Ice\\Widget\\Model_Table' => [
		                'type' => 'span',
		                'roles' => [
		                    0 => 'ROLE_ICE_GUEST',
		                    1 => 'ROLE_ICE_USER',
		                ],
		            ],
		        ],
		        'token' => [
		            'scheme' => [
		                'extra' => '',
		                'type' => 'varchar(64)',
		                'dataType' => 'varchar',
		                'length' => '64',
		                'characterSet' => 'utf8',
		                'nullable' => true,
		                'default' => null,
		                'comment' => '',
		            ],
		            'fieldName' => 'token',
		            'Ice\\Widget\\Model_Form' => [
		                'type' => 'text',
		                'roles' => [
		                    0 => 'ROLE_ICE_GUEST',
		                    1 => 'ROLE_ICE_USER',
		                ],
		            ],
		            'Ice\\Core\\Validator' => [
		                'Ice:Length_Max' => 64,
		            ],
		            'Ice\\Widget\\Model_Table' => [
		                'type' => 'span',
		                'roles' => [
		                    0 => 'ROLE_ICE_GUEST',
		                    1 => 'ROLE_ICE_USER',
		                ],
		            ],
		        ],
		        'token_expired' => [
		            'scheme' => [
		                'extra' => '',
		                'type' => 'datetime',
		                'dataType' => 'datetime',
		                'length' => '0',
		                'characterSet' => null,
		                'nullable' => true,
		                'default' => null,
		                'comment' => '',
		            ],
		            'fieldName' => 'token_expired',
		            'Ice\\Widget\\Model_Form' => [
		                'type' => 'date',
		                'roles' => [
		                    0 => 'ROLE_ICE_GUEST',
		                    1 => 'ROLE_ICE_USER',
		                ],
		            ],
		            'Ice\\Core\\Validator' => [],
		            'Ice\\Widget\\Model_Table' => [
		                'type' => 'span',
		                'roles' => [
		                    0 => 'ROLE_ICE_GUEST',
		                    1 => 'ROLE_ICE_USER',
		                ],
		            ],
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
		        'manyToOne' => [
		            'Ebs\\Model\\Account_Email_Password' => 'token_id',
		            'Ebs\\Model\\Account_Login_Password' => 'token_id',
		        ],
		        'manyToMany' => [
		            'Ebs\\Model\\User' => 'Ebs\\Model\\Account_Login_Password',
		        ],
		    ],
		    'revision' => '10201240_4ng',
		];
    }
}