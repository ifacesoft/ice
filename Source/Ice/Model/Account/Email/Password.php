<?php namespace Ebs\Model;

use Ice\Core\Model;

/**
 * Class Account_Email_Password
 *
 * @property mixed account_email_password_pk
 * @property mixed email
 * @property mixed password
 * @property mixed user__fk
 *
 * @see Ice\Core\Model
 *
 * @package Ebs\Model
 */
class Account_Email_Password extends Model
{
    protected static function config()
    {
        return [
		    'dataSourceKey' => 'Ice\\Data\\Source\\Mysqli/default.lan',
		    'scheme' => [
		        'tableName' => 'ice_account_email_password',
		        'engine' => 'InnoDB',
		        'charset' => 'utf8_general_ci',
		        'comment' => '',
		    ],
		    'columns' => [
		        'id' => [
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
		            'fieldName' => 'account_email_password_pk',
		            'Ice\\Core\\Widget_Form' => 'Number',
		            'Ice\\Core\\Validator' => [],
		            'Ice\\Core\\Widget_Data' => 'text',
		        ],
		        'email' => [
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
		            'fieldName' => 'email',
		            'Ice\\Core\\Widget_Form' => 'Text',
		            'Ice\\Core\\Validator' => [
		                'Ice:Length_Max' => 255,
		            ],
		            'Ice\\Core\\Widget_Data' => 'text',
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
		            'Ice\\Core\\Widget_Form' => 'Text',
		            'Ice\\Core\\Validator' => [
		                'Ice:Length_Max' => 255,
		            ],
		            'Ice\\Core\\Widget_Data' => 'text',
		        ],
		        'user_id' => [
		            'scheme' => [
		                'extra' => '',
		                'type' => 'int(11)',
		                'dataType' => 'int',
		                'length' => '10,0',
		                'characterSet' => null,
		                'nullable' => true,
		                'default' => null,
		                'comment' => '',
		            ],
		            'fieldName' => 'user__fk',
		            'Ice\\Core\\Widget_Form' => 'Number',
		            'Ice\\Core\\Validator' => [],
		            'Ice\\Core\\Widget_Data' => 'text',
		        ],
		    ],
		    'indexes' => [
		        'PRIMARY KEY' => [
		            'PRIMARY' => [
		                1 => 'id',
		            ],
		        ],
		        'FOREIGN KEY' => [
		            'fk_ebs_account_email_password_fos_user_user' => [
		                'fk_ebs_account_email_password_fos_user_user' => 'user_id',
		            ],
		        ],
		        'UNIQUE' => [
		            'email' => [
		                1 => 'email',
		            ],
		        ],
		    ],
		    'references' => [
		        'fos_user_user' => [
		            'constraintName' => 'fk_ebs_account_email_password_fos_user_user',
		            'onUpdate' => 'NO ACTION',
		            'onDelete' => 'NO ACTION',
		        ],
		    ],
		    'relations' => [
		        'oneToMany' => [
		            'fos_user_user' => 'user_id',
		        ],
		        'manyToOne' => [],
		        'manyToMany' => [],
		    ],
		    'revision' => '07281126_thx',
		];
    }
}