<?php

namespace Ice\Model;

use Ice\Core\Exception;
use Ice\Core\Model_Account;
use Ice\Exception\Config_Error;
use Ice\Exception\Error;
use Ice\Exception\FileNotFound;
use Ice\Widget\Account_Form;

class Account_Password_Phone extends Model_Account
{
    protected static function config()
    {
        return [
            'dataSourceKey' => 'Ice\DataSource\Mysqli/default.test',
            'scheme' => [
                'tableName' => 'ice_account_password_phone',
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
                    'fieldName' => 'account_password_phone_pk',
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
                'account_password_phone_expired' => [
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
                    'fieldName' => 'account_password_phone_expired',
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
                'FOREIGN KEY' => [],
                'UNIQUE' => [],
            ],
            'references' => [],
            'relations' => [
                'oneToMany' => [],
                'manyToOne' => [],
                'manyToMany' => [],
            ],
            'revision' => '08071232_r0w',
            'moduleAlias' => 'Ice',
            'modelClass' => 'Ice\Model\Account_Password_Phone',
        ];
    }

    public function prolongate($expired)
    {
        throw new Error('Implement prolongate() method.');
    }

    /**
     * @param Account_Form $accountForm
     * @return array|null
     */
    public function registerVerify(Account_Form $accountForm)
    {
        return $accountForm->validate();
    }

    /**
     * @param Account_Form $accountForm
     * @return array|void
     */
    public function loginVerify(Account_Form $accountForm)
    {
        return $accountForm->validate();
    }

    protected function getAccountData(Account_Form $accountForm)
    {
        return [
            'phone' => $accountForm->get('phone'),
            'password' => password_hash($accountForm->get('password'), PASSWORD_DEFAULT)
        ];
    }

    /**
     * @param Account_Form $accountForm
     * @param array $container
     * @return array
     * @throws Exception
     * @throws Config_Error
     * @throws Error
     * @throws FileNotFound
     */
    protected function getUserData(Account_Form $accountForm, array $container = [])
    {
        return [
            '/login' => $accountForm->get('phone'),
            '/phone' => $accountForm->get('phone')
        ];
    }

    protected function sendConfirmToken(Account_Form $accountForm, Token $token)
    {
        // TODO: Implement sendRegisterConfirm() method.
    }

    protected function isConfirmed(Account_Form $accountForm = null)
    {
        // TODO: Implement isConfirmed() method.
    }

    protected function getConfirmToken(Account_Form $accountForm)
    {
        // TODO: Implement getConfirmToken() method.
    }
}
