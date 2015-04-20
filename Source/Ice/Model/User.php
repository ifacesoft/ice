<?php namespace Ice\Model;

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
 * @see Ice\Core\Model
 *
 * @package Ice\Model
 */
class User extends Model
{
    protected static function config()
    {
        return [
            'dataSourceKey' => 'Ice\\Data\\Source\\Mysqli/default.test',
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
                    'schemeHash' => 708064701,
                    'fieldName' => 'user_pk',
                    'Ice\\Core\\Ui_Form' => 'Number',
                    'Ice\\Core\\Validator' => [],
                    'Ice\\Core\\Ui_Data' => 'text',
                ],
                'user_phone' => [
                    'scheme' => [
                        'extra' => '',
                        'type' => 'varchar(11)',
                        'dataType' => 'varchar',
                        'length' => '11',
                        'characterSet' => 'utf8',
                        'nullable' => true,
                        'default' => null,
                        'comment' => '',
                    ],
                    'schemeHash' => 1706754279,
                    'fieldName' => 'user_phone',
                    'Ice\\Core\\Ui_Form' => 'Text',
                    'Ice\\Core\\Validator' => [
                        'Ice:Length_Max' => 11,
                    ],
                    'Ice\\Core\\Ui_Data' => 'text',
                ],
                'user_email' => [
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
                    'schemeHash' => 1375787174,
                    'fieldName' => 'user_email',
                    'Ice\\Core\\Ui_Form' => 'Text',
                    'Ice\\Core\\Validator' => [
                        'Ice:Length_Max' => 255,
                    ],
                    'Ice\\Core\\Ui_Data' => 'text',
                ],
                'user_name' => [
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
                    'schemeHash' => 1375787174,
                    'fieldName' => 'user_name',
                    'Ice\\Core\\Ui_Form' => 'Text',
                    'Ice\\Core\\Validator' => [
                        'Ice:Length_Max' => 255,
                    ],
                    'Ice\\Core\\Ui_Data' => 'text',
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
                    'schemeHash' => 1375787174,
                    'fieldName' => 'surname',
                    'Ice\\Core\\Ui_Form' => 'Text',
                    'Ice\\Core\\Validator' => [
                        'Ice:Length_Max' => 255,
                    ],
                    'Ice\\Core\\Ui_Data' => 'text',
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
                    'schemeHash' => 1375787174,
                    'fieldName' => 'patronymic',
                    'Ice\\Core\\Ui_Form' => 'Text',
                    'Ice\\Core\\Validator' => [
                        'Ice:Length_Max' => 255,
                    ],
                    'Ice\\Core\\Ui_Data' => 'text',
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
                    'schemeHash' => 360378374,
                    'fieldName' => 'user_active',
                    'Ice\\Core\\Ui_Form' => 'Checkbox',
                    'Ice\\Core\\Validator' => [
                        0 => 'Ice:Not_Null',
                    ],
                    'Ice\\Core\\Ui_Data' => 'text',
                ],
                'user_created' => [
                    'scheme' => [
                        'extra' => '',
                        'type' => 'timestamp',
                        'dataType' => 'timestamp',
                        'length' => '0',
                        'characterSet' => null,
                        'nullable' => true,
                        'default' => 'CURRENT_TIMESTAMP',
                        'comment' => '',
                    ],
                    'schemeHash' => 1401503322,
                    'fieldName' => 'user_created',
                    'Ice\\Core\\Ui_Form' => 'Date',
                    'Ice\\Core\\Validator' => [],
                    'Ice\\Core\\Ui_Data' => 'text',
                ],
            ],
            'oneToMany' => [],
            'manyToOne' => [
                'ice_account' => 'user__fk',
                'ice_user_role_link' => 'user__fk',
            ],
            'manyToMany' => [
                'ice_role' => 'ice_user_role_link',
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
            'revision' => '04182041_u2',
            'schemeHash' => 1930610722,
            'indexesHash' => 329436665,
            'referencesHash' => 223132457,
        ];
    }
}
