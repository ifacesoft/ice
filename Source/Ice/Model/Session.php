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
		    'dataSourceKey' => 'Ice\DataSource\Mysqli/default.ebs_stat',
		    'scheme' => [
		        'tableName' => 'ice_session',
		        'engine' => 'InnoDB',
		        'charset' => 'utf8_general_ci',
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
		            ],
		            'Ice\Core\Validator' => [
		                'Ice:Length_Max' => 128,
		            ],
		            'Ice\Widget\Model_Table' => [
		                'type' => 'span',
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
		            ],
		            'Ice\Core\Validator' => [
		                'Ice:Length_Max' => 65535,
		                0 => 'Ice:Not_Null',
		            ],
		            'Ice\Widget\Model_Table' => [
		                'type' => 'span',
		            ],
		        ],
		        'session__fk' => [
		            'scheme' => [
		                'extra' => '',
		                'type' => 'varbinary(128)',
		                'dataType' => 'varbinary',
		                'length' => '128',
		                'characterSet' => null,
		                'nullable' => true,
		                'default' => null,
		                'comment' => '',
		            ],
		            'fieldName' => 'session__fk',
		            'Ice\Widget\Model_Form' => [
		                'type' => 'text',
		            ],
		            'Ice\Core\Validator' => [
		                'Ice:Length_Max' => 128,
		            ],
		            'Ice\Widget\Model_Table' => [
		                'type' => 'span',
		            ],
		        ],
		        'session_created_at' => [
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
		            'fieldName' => 'session_created_at',
		            'Ice\Widget\Model_Form' => [
		                'type' => 'date',
		            ],
		            'Ice\Core\Validator' => [
		                0 => 'Ice:Not_Null',
		            ],
		            'Ice\Widget\Model_Table' => [
		                'type' => 'span',
		            ],
		        ],
		        'session_updated_at' => [
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
		            'fieldName' => 'session_updated_at',
		            'Ice\Widget\Model_Form' => [
		                'type' => 'date',
		            ],
		            'Ice\Core\Validator' => [
		                0 => 'Ice:Not_Null',
		            ],
		            'Ice\Widget\Model_Table' => [
		                'type' => 'span',
		            ],
		        ],
		        'session_deleted_at' => [
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
		            'fieldName' => 'session_deleted_at',
		            'Ice\Widget\Model_Form' => [
		                'type' => 'date',
		            ],
		            'Ice\Core\Validator' => [
		                0 => 'Ice:Not_Null',
		            ],
		            'Ice\Widget\Model_Table' => [
		                'type' => 'span',
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
		            ],
		            'Ice\Core\Validator' => [
		                'Ice:Length_Max' => 7,
		                0 => 'Ice:Not_Null',
		            ],
		            'Ice\Widget\Model_Table' => [
		                'type' => 'span',
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
		            ],
		            'Ice\Core\Validator' => [
		                0 => 'Ice:Not_Null',
		            ],
		            'Ice\Widget\Model_Table' => [
		                'type' => 'span',
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
		            ],
		            'Ice\Core\Validator' => [
		                'Ice:Length_Max' => 15,
		                0 => 'Ice:Not_Null',
		            ],
		            'Ice\Widget\Model_Table' => [
		                'type' => 'span',
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
		            ],
		            'Ice\Core\Validator' => [
		                'Ice:Length_Max' => 255,
		                0 => 'Ice:Not_Null',
		            ],
		            'Ice\Widget\Model_Table' => [
		                'type' => 'span',
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
		            ],
		            'Ice\Core\Validator' => [
		                0 => 'Ice:Not_Null',
		            ],
		            'Ice\Widget\Model_Table' => [
		                'type' => 'span',
		            ],
		        ],
		        'byIp' => [
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
		            'fieldName' => 'byip',
		            'Ice\Widget\Model_Form' => [
		                'type' => 'checkbox',
		            ],
		            'Ice\Core\Validator' => [
		                0 => 'Ice:Not_Null',
		            ],
		            'Ice\Widget\Model_Table' => [
		                'type' => 'span',
		            ],
		        ],
		        'byLk' => [
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
		            'fieldName' => 'bylk',
		            'Ice\Widget\Model_Form' => [
		                'type' => 'checkbox',
		            ],
		            'Ice\Core\Validator' => [
		                0 => 'Ice:Not_Null',
		            ],
		            'Ice\Widget\Model_Table' => [
		                'type' => 'span',
		            ],
		        ],
		    ],
		    'indexes' => [
		        'PRIMARY KEY' => [
		            'PRIMARY' => [
		                1 => 'session_pk',
		            ],
		        ],
		        'FOREIGN KEY' => [
		            'session__fk' => [
		                'FK_ice_session_ice_session' => 'session__fk',
		            ],
		        ],
		        'UNIQUE' => [],
		    ],
		    'references' => [
		        'ice_session' => [
		            'constraintName' => 'FK_ice_session_ice_session',
		            'onUpdate' => 'NO ACTION',
		            'onDelete' => 'NO ACTION',
		        ],
		    ],
		    'relations' => [
		        'oneToMany' => [
		            'Ice\Model\Session' => 'session__fk',
		        ],
		        'manyToOne' => [
		            'Ice\Model\Session' => 'session__fk',
		        ],
		        'manyToMany' => [],
		    ],
		    'revision' => '02090848_zep',
		];
    }
}