<?php namespace Ice\Model;

use Ice\Core\Model;

/**
 * Class Role
 *
 * @property mixed role_pk
 * @property mixed role_name
 *
 * @see Ice\Core\Model
 *
 * @package Ice\Model
 */
class Role extends Model
{
    protected static function config()
    {
        return [
            'dataSourceKey' => 'Ice\\Data\\Source\\Mysqli/default.test',
            'scheme' => [
                'tableName' => 'ice_role',
                'engine' => 'InnoDB',
                'charset' => 'utf8_general_ci',
                'comment' => '',
            ],
            'columns' => [
                'role_pk' => [
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
                    'fieldName' => 'role_pk',
                    'Ice\\Core\\Widget_Form' => 'Number',
                    'Ice\\Core\\Validator' => [],
                    'Ice\\Core\\Widget_Data' => 'text',
                ],
                'role_name' => [
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
                    'fieldName' => 'role_name',
                    'Ice\\Core\\Widget_Form' => 'Text',
                    'Ice\\Core\\Validator' => [
                        'Ice:Length_Max' => 255,
                    ],
                    'Ice\\Core\\Widget_Data' => 'text',
                ],
            ],
            'oneToMany' => [],
            'manyToOne' => [
                'ice_user_role_link' => 'role__fk',
            ],
            'manyToMany' => [
                'ice_user' => 'ice_user_role_link',
            ],
            'indexes' => [
                'PRIMARY KEY' => [
                    'PRIMARY' => [
                        1 => 'role_pk',
                    ],
                ],
                'FOREIGN KEY' => [],
                'UNIQUE' => [],
            ],
            'references' => [],
            'revision' => '04182041_fo',
            'schemeHash' => 2629563476,
            'indexesHash' => 1312873664,
            'referencesHash' => 223132457,
        ];
    }
}
