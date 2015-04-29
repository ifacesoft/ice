<?php namespace Ice\Model;

use Ice\Core\Model;

/**
 * Class User_Role_Link
 *
 * @property mixed user__fk
 * @property mixed role__fk
 *
 * @see Ice\Core\Model
 *
 * @package Ice\Model
 */
class User_Role_Link extends Model
{
    protected static function config()
    {
        return [
            'dataSourceKey' => 'Ice\\Data\\Source\\Mysqli/default.test',
            'scheme' => [
                'tableName' => 'ice_user_role_link',
                'engine' => 'InnoDB',
                'charset' => 'utf8_general_ci',
                'comment' => '',
            ],
            'columns' => [
                'user__fk' => [
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
                    'schemeHash' => 2126507909,
                    'fieldName' => 'user__fk',
                    'Ice\\Core\\Widget_Form' => 'Number',
                    'Ice\\Core\\Validator' => [],
                    'Ice\\Core\\Widget_Data' => 'text',
                ],
                'role__fk' => [
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
                    'schemeHash' => 2126507909,
                    'fieldName' => 'role__fk',
                    'Ice\\Core\\Widget_Form' => 'Number',
                    'Ice\\Core\\Validator' => [],
                    'Ice\\Core\\Widget_Data' => 'text',
                ],
            ],
            'oneToMany' => [
                'ice_role' => 'role__fk',
                'ice_user' => 'user__fk',
            ],
            'manyToOne' => [],
            'manyToMany' => [],
            'indexes' => [
                'PRIMARY KEY' => [
                    'PRIMARY' => [
                        1 => 'user__fk',
                        2 => 'role__fk',
                    ],
                ],
                'FOREIGN KEY' => [
                    'fk_ice_user_role_link_ice_role' => [
                        'fk_ice_user_role_link_ice_role' => 'role__fk',
                    ],
                    'PRIMARY' => [
                        'fk_ice_user_role_link_ice_user' => 'user__fk',
                    ],
                ],
                'UNIQUE' => [],
            ],
            'references' => [
                'ice_role' => [
                    'constraintName' => 'fk_ice_user_role_link_ice_role',
                    'onUpdate' => 'NO ACTION',
                    'onDelete' => 'NO ACTION',
                ],
                'ice_user' => [
                    'constraintName' => 'fk_ice_user_role_link_ice_user',
                    'onUpdate' => 'NO ACTION',
                    'onDelete' => 'NO ACTION',
                ],
            ],
            'revision' => '04182041_49',
            'schemeHash' => 3357336535,
            'indexesHash' => 560707170,
            'referencesHash' => 3979959563,
        ];
    }
}
