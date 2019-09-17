<?php namespace Ice\Model;

use Ice\Core\Model;

/**
 * Class Log_Error
 *
 * @property mixed log_error_pk
 * @property mixed ip
 * @property mixed agent
 * @property mixed referer
 * @property mixed host
 * @property mixed uri
 * @property mixed post__json
 * @property mixed exception__json
 *
 * @see Ice\Core\Model
 *
 * @package Ice\Model
 */
class Log_Error extends Model
{
    protected static function config()
    {
        return [
		    'dataSourceKey' => 'Ice\Data\Source\Mysqli/default.www',
		    'scheme' => [
		        'tableName' => 'ice_log_error',
		        'engine' => 'InnoDB',
		        'charset' => 'utf8_general_ci',
		        'comment' => '',
		    ],
		    'columns' => [
		        'log_error_pk' => [
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
		            'fieldName' => 'log_error_pk',
		            'Ice\Core\Widget_Form' => 'Number',
		            'Ice\Core\Validator' => [],
		            'Ice\Core\Widget_Data' => 'text',
		        ],
		        'ip' => [
		            'scheme' => [
		                'extra' => '',
		                'type' => 'varchar(15)',
		                'dataType' => 'varchar',
		                'length' => '15',
		                'characterSet' => 'utf8',
		                'nullable' => true,
		                'default' => null,
		                'comment' => '',
		            ],
		            'fieldName' => 'ip',
		            'Ice\Core\Widget_Form' => 'Text',
		            'Ice\Core\Validator' => [
		                'Ice:Length_Max' => 15,
		            ],
		            'Ice\Core\Widget_Data' => 'text',
		        ],
		        'agent' => [
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
		            'fieldName' => 'agent',
		            'Ice\Core\Widget_Form' => 'Text',
		            'Ice\Core\Validator' => [
		                'Ice:Length_Max' => 255,
		            ],
		            'Ice\Core\Widget_Data' => 'text',
		        ],
		        'referer' => [
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
		            'fieldName' => 'referer',
		            'Ice\Core\Widget_Form' => 'Text',
		            'Ice\Core\Validator' => [
		                'Ice:Length_Max' => 255,
		            ],
		            'Ice\Core\Widget_Data' => 'text',
		        ],
		        'host' => [
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
		            'fieldName' => 'host',
		            'Ice\Core\Widget_Form' => 'Text',
		            'Ice\Core\Validator' => [
		                'Ice:Length_Max' => 255,
		            ],
		            'Ice\Core\Widget_Data' => 'text',
		        ],
		        'uri' => [
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
		            'fieldName' => 'uri',
		            'Ice\Core\Widget_Form' => 'Text',
		            'Ice\Core\Validator' => [
		                'Ice:Length_Max' => 255,
		            ],
		            'Ice\Core\Widget_Data' => 'text',
		        ],
		        'post__json' => [
		            'scheme' => [
		                'extra' => '',
		                'type' => 'text',
		                'dataType' => 'text',
		                'length' => '65535',
		                'characterSet' => 'utf8',
		                'nullable' => true,
		                'default' => '[]',
		                'comment' => '',
		            ],
		            'fieldName' => 'post__json',
		            'Ice\Core\Widget_Form' => 'Textarea',
		            'Ice\Core\Validator' => [
		                'Ice:Length_Max' => 65535,
		            ],
		            'Ice\Core\Widget_Data' => 'text',
		        ],
		        'exception__json' => [
		            'scheme' => [
		                'extra' => '',
		                'type' => 'text',
		                'dataType' => 'text',
		                'length' => '65535',
		                'characterSet' => 'utf8',
		                'nullable' => true,
		                'default' => '[]',
		                'comment' => '',
		            ],
		            'fieldName' => 'exception__json',
		            'Ice\Core\Widget_Form' => 'Textarea',
		            'Ice\Core\Validator' => [
		                'Ice:Length_Max' => 65535,
		            ],
		            'Ice\Core\Widget_Data' => 'text',
		        ],
		        'log_error_create_date' => [
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
		            'fieldName' => 'log_error_create_date',
		            'Ice\Core\Widget_Form' => 'Date',
		            'Ice\Core\Validator' => [],
		            'Ice\Core\Widget_Data' => 'text',
		        ],
		    ],
		    'indexes' => [
		        'PRIMARY KEY' => [
		            'PRIMARY' => [
		                1 => 'log_error_pk',
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
		    'revision' => '05061618_mqw',
		];
    }
}