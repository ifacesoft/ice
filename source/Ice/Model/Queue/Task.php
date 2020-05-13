<?php

namespace Ice\Model;

use Ice\Core\Model;
use Ice\Helper\Directory;

/**
 * Class Queue_Task
 *
 * @property mixed queue_task_pk
 * @property mixed queue_job__fk
 * @property mixed queue_task_created_at
 * @property mixed queue_task_started_at
 * @property mixed queue_task_finished_at
 * @property mixed log
 * @property mixed errors
 * @property mixed params__json
 * @property mixed params_hash
 * @property mixed left_key
 * @property mixed right_key
 * @property mixed level
 *
 * @see \Ice\Core\Model
 *
 * @package Ice\Model
 */
class Queue_Task extends Model
{
    protected static function config()
    {
        return [
            'dataSourceKey' => 'Ice\DataSource\Mysqli/default.test',
            'scheme' => [
                'tableName' => 'ice_queue_task',
                'engine' => 'InnoDB',
                'charset' => 'utf8_general_ci',
                'comment' => '',
            ],
            'columns' => [
                'queue_task_pk' => [
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
                    'options' => [
                        'name' => 'queue_task_pk',
                        'type' => 'number',
                    ],
                    'fieldName' => 'queue_task_pk',
                ],
                'queue_task_job_class' => [
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
                    'options' => [
                        'name' => 'queue_task_job_class',
                        'type' => 'text',
                        'validators' => [
                            'Ice:Length_Max' => 255,
                        ],
                    ],
                    'fieldName' => 'queue_task_job_class',
                ],
                'queue_task_active' => [
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
                    'options' => [
                        'name' => 'queue_task_active',
                        'type' => 'checkbox',
                        'validators' => [
                            0 => 'Ice:Not_Null',
                        ],
                    ],
                    'fieldName' => 'queue_task_active',
                ],
                'queue_task_created_at' => [
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
                    'options' => [
                        'name' => 'queue_task_created_at',
                        'type' => 'date',
                        'validators' => [
                            0 => 'Ice:Not_Null',
                        ],
                    ],
                    'fieldName' => 'queue_task_created_at',
                ],
                'queue_task_started_at' => [
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
                    'options' => [
                        'name' => 'queue_task_started_at',
                        'type' => 'date',
                    ],
                    'fieldName' => 'queue_task_started_at',
                ],
                'queue_task_finished_at' => [
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
                    'options' => [
                        'name' => 'queue_task_finished_at',
                        'type' => 'date',
                    ],
                    'fieldName' => 'queue_task_finished_at',
                ],
                'log' => [
                    'scheme' => [
                        'extra' => '',
                        'type' => 'longtext',
                        'dataType' => 'longtext',
                        'length' => '4294967295',
                        'characterSet' => 'utf8',
                        'nullable' => true,
                        'default' => null,
                        'comment' => '',
                    ],
                    'options' => [
                        'name' => 'log',
                        'type' => 'textarea',
                        'validators' => [
                            'Ice:Length_Max' => 4294967295,
                        ],
                    ],
                    'fieldName' => 'log',
                ],
                'errors' => [
                    'scheme' => [
                        'extra' => '',
                        'type' => 'longtext',
                        'dataType' => 'longtext',
                        'length' => '4294967295',
                        'characterSet' => 'utf8',
                        'nullable' => true,
                        'default' => null,
                        'comment' => '',
                    ],
                    'options' => [
                        'name' => 'errors',
                        'type' => 'textarea',
                        'validators' => [
                            'Ice:Length_Max' => 4294967295,
                        ],
                    ],
                    'fieldName' => 'errors',
                ],
                'params__json' => [
                    'scheme' => [
                        'extra' => '',
                        'type' => 'longtext',
                        'dataType' => 'longtext',
                        'length' => '4294967295',
                        'characterSet' => 'utf8',
                        'nullable' => true,
                        'default' => '[]',
                        'comment' => '',
                    ],
                    'options' => [
                        'name' => 'params__json',
                        'type' => 'textarea',
                        'validators' => [
                            'Ice:Length_Max' => 4294967295,
                        ],
                    ],
                    'fieldName' => 'params__json',
                ],
                'params_hash' => [
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
                    'options' => [
                        'name' => 'params_hash',
                        'type' => 'text',
                        'validators' => [
                            'Ice:Length_Max' => 255,
                            0 => 'Ice:Not_Null',
                        ],
                    ],
                    'fieldName' => 'params_hash',
                ],
                'left_key' => [
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
                    'options' => [
                        'name' => 'left_key',
                        'type' => 'number',
                        'validators' => [
                            0 => 'Ice:Not_Null',
                        ],
                    ],
                    'fieldName' => 'left_key',
                ],
                'right_key' => [
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
                    'options' => [
                        'name' => 'right_key',
                        'type' => 'number',
                        'validators' => [
                            0 => 'Ice:Not_Null',
                        ],
                    ],
                    'fieldName' => 'right_key',
                ],
                'level' => [
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
                    'options' => [
                        'name' => 'level',
                        'type' => 'number',
                        'validators' => [
                            0 => 'Ice:Not_Null',
                        ],
                    ],
                    'fieldName' => 'level',
                ],
                'result__json' => [
                    'scheme' => [
                        'extra' => '',
                        'type' => 'longtext',
                        'dataType' => 'longtext',
                        'length' => '4294967295',
                        'characterSet' => 'utf8',
                        'nullable' => true,
                        'default' => '[]',
                        'comment' => '',
                    ],
                    'options' => [
                        'name' => 'result__json',
                        'type' => 'textarea',
                        'validators' => [
                            'Ice:Length_Max' => 4294967295,
                        ],
                    ],
                    'fieldName' => 'result__json',
                ],
                'queue_task_priority' => [
                    'scheme' => [
                        'extra' => '',
                        'type' => 'int(11)',
                        'dataType' => 'int',
                        'length' => '10,0',
                        'characterSet' => null,
                        'nullable' => false,
                        'default' => '1000',
                        'comment' => '',
                    ],
                    'options' => [
                        'name' => 'queue_task_priority',
                        'type' => 'number',
                        'validators' => [
                            0 => 'Ice:Not_Null',
                        ],
                    ],
                    'fieldName' => 'queue_task_priority',
                ],
                'user_id' => [
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
                    'options' => [
                        'name' => 'user__fk',
                        'type' => 'number',
                    ],
                    'fieldName' => 'user__fk',
                ],
                'queue_task_scheduled_at' => [
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
                    'options' => [
                        'name' => 'queue_task_scheduled_at',
                        'type' => 'date',
                    ],
                    'fieldName' => 'queue_task_scheduled_at',
                ],
            ],
            'indexes' => [
                'PRIMARY KEY' => [
                    'PRIMARY' => [
                        1 => 'queue_task_pk',
                    ],
                ],
                'FOREIGN KEY' => [],
                'UNIQUE' => [
                    'queue_job__fk_params_hash' => [
                        1 => 'queue_task_job_class',
                        2 => 'params_hash',
                    ],
                ],
            ],
            'references' => [],
            'relations' => [
                'oneToMany' => [],
                'manyToOne' => [],
                'manyToMany' => [],
            ],
            'revision' => '02261509_vmv',
            'moduleAlias' => 'Ice',
        ];
    }

    /**
     * @throws \Ice\Core\Exception
     */
    protected function beforeInsert()
    {
        parent::beforeInsert();

        $this->set('params_hash', md5($this->get('params__json')));
    }

    /**
     * @param string $path
     * @param bool $isCreate
     * @return string
     * @throws \Ice\Core\Exception
     */
    public function getTempDir($path = '', $isCreate = false) {
        return Directory::get(getTempDir() . 'queue/task/' . $this->getPkValue() . '/');
    }

    /**
     * @return string
     * @throws \Ice\Core\Exception
     */
    public function getBackupDir() {
        return Directory::get(getBackupDir() . 'queue/task/' . $this->getPkValue() . '/');
    }
}