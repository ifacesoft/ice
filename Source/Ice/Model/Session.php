<?php namespace Ice\Model;

use Ice\Core\Model;

/**
 * Class Ice_Session
 *
 * @property mixed session_pk
 * @property mixed session_data
 * @property mixed session_time
 * @property mixed session_lifetime
 *
 * @see Ice\Core\Model
 *
 * @package Ice\Model
 */
class Session extends Model
{
    protected static function config()
    {
        return [
		    'dataSourceKey' => 'Ice\Data\Source\Mysqli/default.ebs_stat',
		    'scheme' => [
		        'tableName' => 'ice_session',
		        'engine' => 'InnoDB',
		        'charset' => 'utf8_bin',
		        'comment' => '',
		    ],
		    'columns' => [
		        'session_pk' => [
		            'scheme' => [
		                'extra' => '',
		                'type' => 'varbinary(128)',
		                'dataType' => 'varbinary',
		                'length' => '128',
		                'characterSet' => null,
		                'nullable' => false,
		                'default' => null,
		                'comment' => '',
		            ],
		            'fieldName' => 'session_pk',
		            'Ice\Widget\Model_Form' => [
		                'type' => 'text',
		                'roles' => [
		                    0 => 'ROLE_ICE_GUEST',
		                    1 => 'ROLE_ICE_USER',
		                ],
		            ],
		            'Ice\Core\Validator' => [
		                'Ice:Length_Max' => 128,
		            ],
		            'Ice\Widget\Model_Table' => [
		                'type' => 'span',
		                'roles' => [
		                    0 => 'ROLE_ICE_GUEST',
		                    1 => 'ROLE_ICE_USER',
		                ],
		            ],
		        ],
		        'session_data' => [
		            'scheme' => [
		                'extra' => '',
		                'type' => 'blob',
		                'dataType' => 'blob',
		                'length' => '65535',
		                'characterSet' => null,
		                'nullable' => false,
		                'default' => null,
		                'comment' => '',
		            ],
		            'fieldName' => 'session_data',
		            'Ice\Widget\Model_Form' => [
		                'type' => 'text',
		                'roles' => [
		                    0 => 'ROLE_ICE_GUEST',
		                    1 => 'ROLE_ICE_USER',
		                ],
		            ],
		            'Ice\Core\Validator' => [
		                'Ice:Length_Max' => 65535,
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
		        'session_create_time' => [
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
		            'fieldName' => 'session_create_time',
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
		        'session_update_time' => [
		            'scheme' => [
		                'extra' => '',
		                'type' => 'int(10) unsigned',
		                'dataType' => 'int',
		                'length' => '10,0',
		                'characterSet' => null,
		                'nullable' => false,
		                'default' => null,
		                'comment' => '',
		            ],
		            'fieldName' => 'session_update_time',
		            'Ice\Widget\Model_Form' => [
		                'type' => 'number',
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
		        'session_lifetime' => [
		            'scheme' => [
		                'extra' => '',
		                'type' => 'mediumint(9)',
		                'dataType' => 'mediumint',
		                'length' => '7,0',
		                'characterSet' => null,
		                'nullable' => false,
		                'default' => null,
		                'comment' => '',
		            ],
		            'fieldName' => 'session_lifetime',
		            'Ice\Widget\Model_Form' => [
		                'type' => 'text',
		                'roles' => [
		                    0 => 'ROLE_ICE_GUEST',
		                    1 => 'ROLE_ICE_USER',
		                ],
		            ],
		            'Ice\Core\Validator' => [
		                'Ice:Length_Max' => 7,
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
		        'ip' => [
		            'scheme' => [
		                'extra' => '',
		                'type' => 'varchar(15)',
		                'dataType' => 'varchar',
		                'length' => '15',
		                'characterSet' => 'utf8',
		                'nullable' => false,
		                'default' => null,
		                'comment' => '',
		            ],
		            'fieldName' => 'ip',
		            'Ice\Widget\Model_Form' => [
		                'type' => 'text',
		                'roles' => [
		                    0 => 'ROLE_ICE_GUEST',
		                    1 => 'ROLE_ICE_USER',
		                ],
		            ],
		            'Ice\Core\Validator' => [
		                'Ice:Length_Max' => 15,
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
		        'agent' => [
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
		            'fieldName' => 'agent',
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
		            'fieldName' => 'user__fk',
		            'Ice\Widget\Model_Form' => [
		                'type' => 'number',
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
		        'views' => [
		            'scheme' => [
		                'extra' => '',
		                'type' => 'int(11)',
		                'dataType' => 'int',
		                'length' => '10,0',
		                'characterSet' => null,
		                'nullable' => false,
		                'default' => null,
		                'comment' => '',
		            ],
		            'fieldName' => 'views',
		            'Ice\Widget\Model_Form' => [
		                'type' => 'number',
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
		    ],
		    'indexes' => [
		        'PRIMARY KEY' => [
		            'PRIMARY' => [
		                1 => 'session_pk',
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
		    'revision' => '02021334_90t',
		];
    }
}