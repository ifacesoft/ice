<?php namespace Ice\Model;

use Ice\Core\Model;

/**
 * Class Token
 *
 * @property mixed token_pk
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
		    'dataSourceKey' => 'Ice\DataSource\Mysqli/default.ebs',
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
		            'fieldName' => 'token_pk',
		            'Ice\Widget\Model_Form' => [
		                'type' => 'number',
		                'roles' => [
		                    0 => 'ROLE_ICE_GUEST',
		                    1 => 'ROLE_ICE_USER',
		                ],
		            ],
		            'Ice\Core\Validator' => [],
		            'Ice\Widget\Model_Table' => [
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
		            'Ice\Widget\Model_Form' => [
		                'type' => 'text',
		                'roles' => [
		                    0 => 'ROLE_ICE_GUEST',
		                    1 => 'ROLE_ICE_USER',
		                ],
		            ],
		            'Ice\Core\Validator' => [
		                'Ice:Length_Max' => 64,
		            ],
		            'Ice\Widget\Model_Table' => [
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
		            'Ice\Widget\Model_Form' => [
		                'type' => 'date',
		                'roles' => [
		                    0 => 'ROLE_ICE_GUEST',
		                    1 => 'ROLE_ICE_USER',
		                ],
		            ],
		            'Ice\Core\Validator' => [],
		            'Ice\Widget\Model_Table' => [
		                'type' => 'span',
		                'roles' => [
		                    0 => 'ROLE_ICE_GUEST',
		                    1 => 'ROLE_ICE_USER',
		                ],
		            ],
		        ],
		        'token_data__json' => [
		            'scheme' => [
		                'extra' => '',
		                'type' => 'text',
		                'dataType' => 'text',
		                'length' => '65535',
		                'characterSet' => 'utf8',
		                'nullable' => true,
		                'default' => '[]',
		                'comment' => '',
		            ],
		            'fieldName' => 'token_data__json',
		            'Ice\Widget\Model_Form' => [
		                'type' => 'textarea',
		                'roles' => [
		                    0 => 'ROLE_ICE_GUEST',
		                    1 => 'ROLE_ICE_USER',
		                ],
		            ],
		            'Ice\Core\Validator' => [
		                'Ice:Length_Max' => 65535,
		            ],
		            'Ice\Widget\Model_Table' => [
		                'type' => 'span',
		                'roles' => [
		                    0 => 'ROLE_ICE_GUEST',
		                    1 => 'ROLE_ICE_USER',
		                ],
		            ],
		        ],
		        'modelClass' => [
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
		            'fieldName' => 'modelClass',
		            'Ice\Widget\Model_Form' => [
		                'type' => 'text',
		                'roles' => [
		                    0 => 'ROLE_ICE_GUEST',
		                    1 => 'ROLE_ICE_USER',
		                ],
		            ],
		            'Ice\Core\Validator' => [
		                'Ice:Length_Max' => 255,
		            ],
		            'Ice\Widget\Model_Table' => [
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
		        'UNIQUE' => [
		            'token' => [
		                1 => 'token',
		            ],
		        ],
		    ],
		    'references' => [],
		    'relations' => [
		        'oneToMany' => [],
		        'manyToOne' => [
		            'Ebs\Model\Account_Email_Password' => 'token_id',
		            'Ebs\Model\Account_Login_Password' => 'token_id',
		        ],
		        'manyToMany' => [
		            'Ebs\Model\User' => [
		                0 => 'Ebs\Model\Account_Email_Password',
		                1 => 'Ebs\Model\Account_Login_Password',
		            ],
		        ],
		    ],
		    'revision' => '10201240_4ng',
		    'modelClass' => 'Ice\Model\Token',
		    'modelPath' => 'Ice/Model/Token.php',
		];
    }
}