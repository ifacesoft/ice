<?php

namespace Ice\Model;

use Ice\Core\Exception;
use Ice\Core\Model;
use Ice\Helper\Date;

/**
 * Class User
 *
 * @property mixed user_pk
 * @property mixed user_phone
 * @property mixed user_email
 * @property mixed user_name
 * @property mixed surname
 * @property mixed patronymic
 * @property mixed user_active
 * @property mixed user_created
 *
 * @see \Ice\Core\Model
 *
 * @package Ice\Model
 */
final class User extends Model
{
    protected static function config()
    {
        return [
            'dataSourceKey' => 'Ice\DataSource\Mysqli/default.test',
            'scheme' => [
                'tableName' => 'ice_user',
                'engine' => 'InnoDB',
                'charset' => 'utf8_general_ci',
                'comment' => 'Пользователи',
            ],
            'columns' => [
                'user_pk' => [
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
                    'fieldName' => 'user_pk',
                    'Ice\Widget\Model_Form' => 'Field_Number',
                    'Ice\Core\Validator' => [],
                    'Ice\Widget\Model_Table' => 'text',
                ],
                'user_name' => [
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
                    'fieldName' => 'user_name',
                    'Ice\Widget\Model_Form' => 'Field_Text',
                    'Ice\Core\Validator' => [
                        'Ice:Length_Max' => 255,
                    ],
                    'Ice\Widget\Model_Table' => 'text',
                ],
                'surname' => [
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
                    'fieldName' => 'surname',
                    'Ice\Widget\Model_Form' => 'Field_Text',
                    'Ice\Core\Validator' => [
                        'Ice:Length_Max' => 255,
                    ],
                    'Ice\Widget\Model_Table' => 'text',
                ],
                'patronymic' => [
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
                    'fieldName' => 'patronymic',
                    'Ice\Widget\Model_Form' => 'Field_Text',
                    'Ice\Core\Validator' => [
                        'Ice:Length_Max' => 255,
                    ],
                    'Ice\Widget\Model_Table' => 'text',
                ],
                'user_active' => [
                    'scheme' => [
                        'extra' => '',
                        'type' => 'tinyint(1)',
                        'dataType' => 'tinyint',
                        'length' => '3,0',
                        'characterSet' => null,
                        'nullable' => false,
                        'default' => '0',
                        'comment' => '',
                    ],
                    'fieldName' => 'user_active',
                    'Ice\Widget\Model_Form' => 'Field_Checkbox',
                    'Ice\Core\Validator' => [
                        0 => 'Ice:Not_Null',
                    ],
                    'Ice\Widget\Model_Table' => 'text',
                ],
                'user_logined_at' => [
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
                    'fieldName' => 'user_logined_at',
                    'Ice\Widget\Model_Form' => 'Field_Date',
                    'Ice\Core\Validator' => [],
                    'Ice\Widget\Model_Table' => 'text',
                ],
                'user_expired_at' => [
                    'scheme' => [
                        'extra' => '',
                        'type' => 'datetime',
                        'dataType' => 'datetime',
                        'length' => '0',
                        'characterSet' => null,
                        'nullable' => false,
                        'default' => '1970-01-01 00:00:00',
                        'comment' => '',
                    ],
                    'fieldName' => 'user_expired_at',
                    'Ice\Widget\Model_Form' => 'Field_Date',
                    'Ice\Core\Validator' => [],
                    'Ice\Widget\Model_Table' => 'text',
                ],
                'user_created_at' => [
                    'scheme' => [
                        'extra' => '',
                        'type' => 'timestamp',
                        'dataType' => 'timestamp',
                        'length' => '0',
                        'characterSet' => null,
                        'nullable' => false,
                        'default' => 'CURRENT_TIMESTAMP',
                        'comment' => '',
                    ],
                    'fieldName' => 'user_created_at',
                    'Ice\Widget\Model_Form' => 'Field_Date',
                    'Ice\Core\Validator' => [],
                    'Ice\Widget\Model_Table' => 'text',
                ],
                'user_updated_at' => [
                    'scheme' => [
                        'extra' => 'on update current_timestamp()',
                        'type' => 'timestamp',
                        'dataType' => 'timestamp',
                        'length' => '0',
                        'characterSet' => null,
                        'nullable' => false,
                        'default' => 'CURRENT_TIMESTAMP',
                        'comment' => '',
                    ],
                    'fieldName' => 'user_updated_at',
                    'Ice\Widget\Model_Form' => 'Field_Date',
                    'Ice\Core\Validator' => [],
                    'Ice\Widget\Model_Table' => 'text',
                ],
            ],
            'indexes' => [
                'PRIMARY KEY' => [
                    'PRIMARY' => [
                        1 => 'user_pk',
                    ],
                ],
                'FOREIGN KEY' => [],
                'UNIQUE' => [
                    'UNIQUE' => [],
                ],
            ],
            'relations' => [
                'manyToOne' => [
                    'Ice\Model\Token' => 'user__fk',
                ],
                'manyToMany' => [],
                'oneToMany' => [],
            ],
            'references' => [],
            'revision' => '05201942_5yc',
            'modelClass' => 'Ice\Model\User',
            'moduleAlias' => 'Ice',
        ];
    }

    /**
     * Check is active user
     *
     * @return bool
     * @throws Exception
     */
    public function isActive()
    {
        return (bool)$this->get('/active');
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function isExpired()
    {
        return Date::expired($this->get('/expired_at'));
    }
}