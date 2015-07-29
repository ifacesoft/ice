<?php namespace Ice\Model;

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
		    'dataSourceKey' => 'Ice\Data\Source\Mysqli/default.lan',
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
		            'Ice\Core\Widget_Form' => 'Field_Number',
		            'Ice\Core\Validator' => [],
		            'Ice\Core\Widget_Data' => 'text',
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
		            'Ice\Core\Widget_Form' => 'Field_Text',
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
		            'Ice\Core\Widget_Form' => 'Field_Text',
		            'Ice\Core\Validator' => [
		                'Ice:Length_Max' => 255,
		            ],
		            'Ice\Core\Widget_Data' => 'text',
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
		            'Ice\Core\Widget_Form' => 'Field_Number',
		            'Ice\Core\Validator' => [],
		            'Ice\Core\Widget_Data' => 'text',
		        ],
		        'account_email_password_key' => [
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
		            'fieldName' => 'account_email_password_key',
		            'Ice\Core\Widget_Form' => 'Field_Text',
		            'Ice\Core\Validator' => [
		                'Ice:Length_Max' => 64,
		            ],
		            'Ice\Core\Widget_Data' => 'text',
		        ],
		        'account_email_password_active' => [
		            'scheme' => [
		                'extra' => '',
		                'type' => 'tinyint(1)',
		                'dataType' => 'tinyint',
		                'length' => '3,0',
		                'characterSet' => null,
		                'nullable' => true,
		                'default' => '0',
		                'comment' => '',
		            ],
		            'fieldName' => 'account_email_password_active',
		            'Ice\Core\Widget_Form' => 'Field_Checkbox',
		            'Ice\Core\Validator' => [],
		            'Ice\Core\Widget_Data' => 'text',
		        ],
		        'account_email_password_key_expired' => [
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
		            'fieldName' => 'account_email_password_key_expired',
		            'Ice\Core\Widget_Form' => 'Field_Date',
		            'Ice\Core\Validator' => [],
		            'Ice\Core\Widget_Data' => 'text',
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
		            'Ebs\Model\User' => 'user_id',
		        ],
		        'manyToOne' => [],
		        'manyToMany' => [],
		    ],
		    'revision' => '07281126_thx',
		    'modelClass' => 'Ice\Model\Account_Email_Password',
		    'modelPath' => 'Ice/Model/Account/Email/Password.php',
		];
    }
}