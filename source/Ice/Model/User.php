<?php

namespace Ice\Model;

use Ice\Core\Model;

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
class User extends Model
{
    protected static function config()
    {
        return [
            'dataSourceKey' => 'Ice\DataSource\Mysqli/default.test',
            'scheme' => [
                'tableName' => 'ice_user',
                'engine' => 'InnoDB',
                'charset' => 'utf8_general_ci',
                'comment' => '',
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
                'user_phone' => [
                    'scheme' => [
                        'extra' => '',
                        'type' => 'varchar(11)',
                        'dataType' => 'varchar',
                        'length' => '11',
                        'characterSet' => 'utf8',
                        'nullable' => false,
                        'default' => null,
                        'comment' => '',
                    ],
                    'fieldName' => 'user_phone',
                    'Ice\Widget\Model_Form' => 'Field_Text',
                    'Ice\Core\Validator' => [
                        'Ice:Length_Max' => 11,
                    ],
                    'Ice\Widget\Model_Table' => 'text',
                ],
                'user_email' => [
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
                    'fieldName' => 'user_email',
                    'Ice\Widget\Model_Form' => 'Field_Text',
                    'Ice\Core\Validator' => [
                        'Ice:Length_Max' => 255,
                    ],
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
                        'nullable' => false,
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
                        'nullable' => false,
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
                        'type' => 'tinyint(4)',
                        'dataType' => 'tinyint',
                        'length' => '3,0',
                        'characterSet' => null,
                        'nullable' => false,
                        'default' => '1',
                        'comment' => '',
                    ],
                    'fieldName' => 'user_active',
                    'Ice\Widget\Model_Form' => 'Field_Checkbox',
                    'Ice\Core\Validator' => [
                        0 => 'Ice:Not_Null',
                    ],
                    'Ice\Widget\Model_Table' => 'text',
                ],
                'user_created' => [
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
                    'fieldName' => 'user_created',
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
                'UNIQUE' => [],
            ],
            'references' => [],
            'relations' => [
                'oneToMany' => [],
                'manyToOne' => [],
                'manyToMany' => [],
            ],
            'revision' => '05201942_5yc',
            'modelClass' => 'Ice\Model\User',
            'moduleAlias' => 'Ice',
        ];
    }

    public function getTimezone()
    {
        if ($timezone = $this->get('timezone', false)) {
            return $timezone;
        }

        return Module::getInstance()->getDefault('date')->get('client_timezone');
    }

    /**
     * Check is active user
     *
     * @return bool
     * @throws \Ice\Core\Exception
     */
    public function isActive()
    {
        return (bool)$this->get('/active', 0);
    }
}