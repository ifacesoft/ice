<?php

namespace Ice\Model;

use Ice\Core\Config;
use Ice\Core\Model;
use Ice\Core\Model\Security_Account;
use Ice\Core\Security;

class Account_Phone_Password extends Model implements Security_Account
{
    protected static function config()
    {
        return [
            'dataSourceKey' => 'Ice\DataSource\Mysqli/default.ebs',
            'scheme' => [
                'tableName' => 'ice_account_phone_password',
                'engine' => 'InnoDB',
                'charset' => 'utf8_general_ci',
                'comment' => 'Авторизация по phone',
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
                    'fieldName' => 'account_phone_password_pk',
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
                'phone' => [
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
                    'fieldName' => 'phone',
                    'Ice\Widget\Model_Form' => [
                        'type' => 'text',
                        'roles' => [
                            0 => 'ROLE_ICE_GUEST',
                            1 => 'ROLE_ICE_USER',
                        ],
                    ],
                    'Ice\Core\Validator' => [
                        'Ice:Length_Max' => 255,
                        0 => 'Ice:Not_Null',
                    ],
                    'Ice\Widget\Model_Table' => [
                        'type' => 'span',
                        'roles' => [
                            0 => 'ROLE_ICE_GUEST',
                            1 => 'ROLE_ICE_USER',
                        ],
                    ],
                ],
                'password' => [
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
                    'fieldName' => 'password',
                    'Ice\Widget\Model_Form' => [
                        'type' => 'text',
                        'roles' => [
                            0 => 'ROLE_ICE_GUEST',
                            1 => 'ROLE_ICE_USER',
                        ],
                    ],
                    'Ice\Core\Validator' => [
                        'Ice:Length_Max' => 255,
                        0 => 'Ice:Not_Null',
                    ],
                    'Ice\Widget\Model_Table' => [
                        'type' => 'span',
                        'roles' => [
                            0 => 'ROLE_ICE_GUEST',
                            1 => 'ROLE_ICE_USER',
                        ],
                    ],
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
                'account_phone_password_expired' => [
                    'scheme' => [
                        'extra' => '',
                        'type' => 'datetime',
                        'dataType' => 'datetime',
                        'length' => '0',
                        'characterSet' => null,
                        'nullable' => false,
                        'default' => null,
                        'comment' => '',
                    ],
                    'fieldName' => 'account_phone_password_expired',
                    'Ice\Widget\Model_Form' => [
                        'type' => 'date',
                        'roles' => [
                            0 => 'ROLE_ICE_GUEST',
                            1 => 'ROLE_ICE_USER',
                        ],
                    ],
                    'Ice\Core\Validator' => [
                        0 => 'Ice:Not_Null',
                    ],
                    'Ice\Widget\Model_Table' => [
                        'type' => 'span',
                        'roles' => [
                            0 => 'ROLE_ICE_GUEST',
                            1 => 'ROLE_ICE_USER',
                        ],
                    ],
                ],
                'token_id' => [
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
                    'fieldName' => 'token__fk',
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
            ],
            'indexes' => [
                'PRIMARY KEY' => [
                    'PRIMARY' => [
                        1 => 'id',
                    ],
                ],
                'FOREIGN KEY' => [
                    'fk_ebs_account_phone_password_fos_user_user' => [
                        'fk_ebs_account_phone_password_fos_user_user' => 'user_id',
                    ],
                    'fk_ebs_account_phone_password_ice_token' => [
                        'fk_ebs_account_phone_password_ice_token' => 'token_id',
                    ],
                ],
                'UNIQUE' => [
                    'phone' => [
                        1 => 'phone',
                    ],
                ],
            ],
            'references' => [
                'fos_user_user' => [
                    'constraintName' => 'fk_ebs_account_phone_password_fos_user_user',
                    'onUpdate' => 'NO ACTION',
                    'onDelete' => 'NO ACTION',
                ],
                'ice_token' => [
                    'constraintName' => 'fk_ebs_account_phone_password_ice_token',
                    'onUpdate' => 'NO ACTION',
                    'onDelete' => 'NO ACTION',
                ],
            ],
            'relations' => [
                'oneToMany' => [
                    'Ebs\Model\User' => 'user_id',
                    'Ice\Model\Token' => 'token_id',
                ],
                'manyToOne' => [],
                'manyToMany' => [],
            ],
            'revision' => '08071232_r0w',
            'modelClass' => 'Ice\Model\Account_Phone_Password',
            'modelPath' => 'Ice/Model/Account/Phone/Password.php',
        ];
    }

    /**
     * Check is expired account
     *
     * @return bool
     */
    public function isExpired()
    {
        return strtotime($this->get('/expired')) < time();
    }

    public function getUser()
    {
        /** @var Model $userModelClass */
        $userModelClass = Config::getInstance(Security::getClass())->get('userModelClass');
        return $this->fetchOne($userModelClass, '*', true);
    }

    public function securityVerify(array $values)
    {
        return password_verify($values['password'], $this->get('password'));
    }

    public function securityHash(array $values)
    {
        return password_hash($values['new_password'], PASSWORD_DEFAULT);
    }
}