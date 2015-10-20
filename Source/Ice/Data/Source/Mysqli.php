<?php
/**
 * Ice data source implementation mysqli class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Data\Source;

use Ice\Core\Converter;
use Ice\Core\Data_Provider;
use Ice\Core\Data_Source;
use Ice\Core\Exception;
use Ice\Core\Logger;
use Ice\Core\Model;
use Ice\Core\Module;
use Ice\Core\Query;
use Ice\Core\Query_Builder;
use Ice\Core\Query_Result;
use Ice\Core\Query_Translator;
use Ice\Exception\DataSource;
use Ice\Exception\DataSource_Insert;
use Ice\Exception\DataSource_Insert_DuplicateEntry;
use Ice\Helper\Arrays;
use Ice\Helper\Model as Helper_Model;
use Ice\Helper\String;
use mysqli_result;
use mysqli_stmt;

/**
 * Class Mysqli
 *
 * Implements mysqli data source
 *
 * @see Ice\Core\Data_Source
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Data_Source
 */
class Mysqli extends Data_Source
{
    const DATA_PROVIDER_CLASS = 'Ice\Data\Provider\Mysqli';
    const QUERY_TRANSLATOR_CLASS = 'Ice\Query\Translator\Sql';

    const TRANSACTION_REPEATABLE_READ = 'REPEATABLE READ';
    const TRANSACTION_READ_COMMITTED = 'READ COMMITTED';
    const TRANSACTION_READ_UNCOMMITTED = 'READ UNCOMMITTED';
    const TRANSACTION_SERIALIZABLE = 'SERIALIZABLE';


    protected $savePointLevel = null;

    /**
     * Execute query select to data source
     *
     * @param  Query $query
     * @return array
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.0
     */
    public function executeSelect(Query $query)
    {
        $data = [];

        $statement = $this->getStatement($query->getBody(), $query->getBinds());

        $logger = Logger::getInstance(__CLASS__);

        if ($statement->execute() === false) {
            $errno = $statement->errno;
            $error = $statement->error;
            $statement->close();

            $logger->exception(
                [
                    '#' . $errno . ': {$0} - {$1} [{$2}]',
                    [$error, print_r($query->getBody(), true), implode(', ', $query->getBinds())]
                ],
                __FILE__,
                __LINE__
            );
        }

        /**
         * @var mysqli_result $result
         */
        $result = $statement->get_result();

        if ($result === false) {
            $errno = $statement->errno;
            $error = $statement->error;
            $statement->close();

            $logger->exception(
                [
                    '#' . $errno . ': {$0} - {$1} [{$2}]',
                    [$error, print_r($query->getBody(), true), implode(', ', $query->getBinds())]
                ],
                __FILE__,
                __LINE__
            );
        }

        $modelClass = $query->getQueryBuilder()->getModelClass();

        $pkFieldNames = $modelClass::getScheme()->getPkFieldNames();

//        $statementResultMetadata = $statement->result_metadata();
//
//        $bindResultVars = [];
//        $row = [];
//        while($field = $statementResultMetadata->fetch_field()) {
//            $bindResultVars[] = &$row[$field->name];
//        }
//
//        call_user_func_array(array($statement, 'bind_result'), $bindResultVars);

        $data[Query_Result::ROWS] = [];

        while ($row = $result->fetch_assoc()) {
            foreach ($query->getAfterSelectTriggers() as list($method, $params)) {
                $row = $modelClass::$method($row, $params);

                if (!$row) {
                    $logger->exception(
                        ['Trigger(method) {$0} of model {$1} must return row. Fix it.', [$method, $modelClass]],
                        __FILE__,
                        __LINE__
                    );
                }
            }

            $id = implode('_', array_intersect_key($row, array_flip($pkFieldNames)));

            if ($id) {
                $data[Query_Result::ROWS][$id] = $row;
            } else {
                $data[Query_Result::ROWS][] = $row;
            }
        }

        $result->free_result();
        $statement->free_result();
        $statement->close();

        unset($result);
        unset($statement);

        $data[Query_Result::NUM_ROWS] = count($data[Query_Result::ROWS]);

        if ($query->isCalcFoundRows()) {
            $result = $this->getConnection()->query('SELECT FOUND_ROWS()');
            $foundRows = $result->fetch_row();
            $result->close();
            $data[Query_Result::FOUND_ROWS] = reset($foundRows);
        } else {
            $data[Query_Result::FOUND_ROWS] = $data[Query_Result::NUM_ROWS];
        }

        foreach ($query->getQueryBuilder()->getTransforms() as list($converterClass, $params)) {
            $data = Converter::getInstance($converterClass)->convert($data, $params);
        }

        return $data;
    }

    /**
     * Prepare query statement for query
     *
     * @param    $body
     * @param    array $binds
     * @return   mysqli_stmt
     * @throws   Exception
     * @internal param array $bodyParts
     * @author   dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since   0.0
     */
    public function getStatement($body, array $binds)
    {
        $statement = $this->getConnection()->prepare($body);

        $logger = Logger::getInstance(__CLASS__);

        if (!$statement) {
            switch ($this->getConnection()->errno) {
                case 1146:
                    $exceptionClass = DataSource_TableNotFound::getClass();
                    break;
                default:
                    $exceptionClass = DataSource::getClass();
            }

            $logger->exception(
                ['#' . $this->getConnection()->errno . ': {$0}', $this->getConnection()->error],
                __FILE__,
                __LINE__,
                null,
                [$body, $binds],
                -1,
                $exceptionClass
            );
        }

        $types = '';
        foreach ($binds as $bind) {
            $types .= gettype($bind)[0];
        }

        $values = [str_replace('N', 's', $types)];

        if (!empty($types)) {
            $values = array_merge($values, $binds);

            if (call_user_func_array(array($statement, 'bind_param'), $this->makeValuesReferenced($values)) === false) {
                $logger->exception(
                    'Bind params failure',
                    __FILE__,
                    __LINE__,
                    null,
                    ['types' => $types, 'values' => $values, 'body' => $body, 'binds' => $binds]
                );
            }
        }

        return $statement;
    }

    /**
     * Get connection instance
     *
     * @param  string|null $scheme
     * @return \Mysqli
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function getConnection($scheme = null)
    {
        return parent::getConnection();
    }

    /**
     * Override values in query binds (man solution)
     *
     * @param  $arr
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    private function makeValuesReferenced($arr)
    {
        $refs = [];
        foreach ($arr as $key => $value) {
            $refs[$key] = &$arr[$key];
        }
        return $refs;
    }

    /**
     * Execute query insert to data source
     *
     * @param  Query $query
     * @return array
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.0
     */
    public function executeInsert(Query $query)
    {
        $data = [];

        $statement = $this->getStatement($query->getBody(), $query->getBinds());

        $logger = Logger::getInstance(__CLASS__);

        if ($statement->execute() === false) {
            $errno = $statement->errno;
            $error = $statement->error;
            $statement->close();

            switch ($errno) {
                case 1062:
                    $exceptionClass = DataSource_Insert_DuplicateEntry::getClass();
                    break;
                default:
                    $exceptionClass = DataSource_Insert::getClass();
            }

            $logger->exception(
                [
                    '#' . $errno . ': {$0} - {$1} [{$2}]',
                    [$error, print_r($query->getBody(), true), implode(', ', $query->getBinds())]
                ],
                __FILE__,
                __LINE__,
                null,
                [$query->getBody(), $query->getBinds()],
                -1,
                $exceptionClass
            );
        }

        /**
         * @var Model $modelClass
         */
        $modelClass = $query->getQueryBuilder()->getModelClass();
        $pkFieldNames = $modelClass::getScheme()->getPkFieldNames();

        $pkFieldName = count($pkFieldNames) == 1 ? reset($pkFieldNames) : null;

        $insertId = $statement->insert_id;

        foreach ($query->getBindParts()[Query_Builder::PART_VALUES] as $row) {
            if ($pkFieldName) {
                if (!isset($row[$pkFieldName])) {
                    $row[$pkFieldName] = $insertId;
                    $insertId++;
                } else {
                    $insertId = null;
                }
            }

            $insertKeys = array_intersect_key($row, array_flip($pkFieldNames));

            $data[Query_Result::INSERT_ID][] = $insertKeys;
            $data[Query_Result::ROWS][implode('_', $insertKeys)] = $row;
        }

        $data[Query_Result::AFFECTED_ROWS] = $statement->affected_rows;

        $statement->close();

        return $data;
    }

    /**
     * Execute query update to data source
     *
     * @param  Query $query
     * @return array
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since   0.0
     */
    public function executeUpdate(Query $query)
    {
        $data = [];

        $statement = $this->getStatement($query->getBody(), $query->getBinds());

        $logger = Logger::getInstance(__CLASS__);

        if ($statement->execute() === false) {
            $errno = $statement->errno;
            $error = $statement->error;
            $statement->close();

            $logger->exception(
                [
                    '#' . $errno . ': {$0} - {$1} [{$2}]',
                    [$error, print_r($query->getBody(), true), implode(', ', $query->getBinds())]
                ],
                __FILE__,
                __LINE__
            );
        }

        /**
         * @var Model $modelclass
         */
        $modelclass = $query->getQueryBuilder()->getModelClass();
        $pkFieldNames = $modelclass::getScheme()->getPkFieldNames();

        foreach ($query->getBindParts()[Query_Builder::PART_SET] as $row) {
            $insertKey = implode('_', array_intersect_key($row, array_flip($pkFieldNames)));
            $data[Query_Result::ROWS][$insertKey] = $row;
        }

        $data[Query_Result::AFFECTED_ROWS] = $statement->affected_rows;

        $statement->close();

        return $data;
    }

    /**
     * Execute query update to data source
     *
     * @param  Query $query
     * @return array
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since   0.0
     */
    public function executeDelete(Query $query)
    {
        $data = [];

        $statement = $this->getStatement($query->getBody(), $query->getBinds());

        $logger = Logger::getInstance(__CLASS__);

        if ($statement->execute() === false) {
            $errno = $statement->errno;
            $error = $statement->error;
            $statement->close();

            $logger->exception(
                [
                    '#' . $errno . ': {$0} - {$1} [{$2}]',
                    [$error, print_r($query->getBody(), true), implode(', ', $query->getBinds())]
                ],
                __FILE__,
                __LINE__
            );
        }

        $data[Query_Result::AFFECTED_ROWS] = $statement->affected_rows;

        $statement->close();

        return $data;
    }

    /**
     * Get data Scheme from data source
     *
     * @param  Module $module
     * @return array
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since   0.0
     */
    public function getTables(Module $module)
    {
        $tables = [];

        $dataProvider = $this->getSourceDataProvider();
        $dataProvider->setScheme('information_schema');

        $tableDefault = [
            'dataSourceKey' => $this->getDataSourceKey(),
            'scheme' => [],
            'columns' => [],
            'indexes' => [],
            'references' => [],
            'relations' => [
                'oneToMany' => [],
                'manyToOne' => [],
                'manyToMany' => [],
            ],
        ];

        foreach ($dataProvider->get('TABLES:TABLE_SCHEMA/' . $this->scheme) as $table) {
            if ($module->checkTableByPrefix($table['TABLE_NAME'], $this->getDataSourceKey())) {
                if (!isset($tables[$table['TABLE_NAME']])) {
                    $tables[$table['TABLE_NAME']] = $tableDefault;
                }

                $data = &$tables[$table['TABLE_NAME']];

                $data['revision'] = date('mdHi') . '_' . strtolower(String::getRandomString(3));

                $dataScheme = &$data['scheme'];
                $dataScheme = [
                    'tableName' => $table['TABLE_NAME'],
                    'engine' => $table['ENGINE'],
                    'charset' => $table['TABLE_COLLATION'],
                    'comment' => $table['TABLE_COMMENT']
                ];

                $data['indexes'] = $this->getIndexes($table['TABLE_NAME']);

                $data['references'] = $this->getReferences($table['TABLE_NAME']);

                foreach ($data['references'] as $tableName => $reference) {
                    foreach ($data['indexes']['FOREIGN KEY'] as $indexes) {
                        foreach ($indexes as $constraintName => $columnNames) {
                            if ($constraintName != $reference['constraintName']) {
                                continue;
                            }

                            $data['relations']['oneToMany'][$tableName] = $columnNames;

                            if (!isset($tables[$tableName])) {
                                $tables[$tableName] = $tableDefault;
                            }

                            $tables[$tableName]['relations']['manyToOne'][$table['TABLE_NAME']] = $columnNames;

                            break;
                        }
                    }
                }

                if (count($data['references'])) {
                    foreach ($data['references'] as $tableName1 => $reference1) {
                        foreach ($data['references'] as $tableName2 => $reference2) {
                            if ($tableName1 != $tableName2) {
                                $tables[$tableName1]['relations']['manyToMany'][$tableName2] = $table['TABLE_NAME'];
                            }
                        }
                    }
                }

                $columns = &$data['columns'];
                foreach ($this->getColumns($table['TABLE_NAME']) as $columnName => $column) {
                    $columns[$columnName]['scheme'] = $column;

                    $tablePrefixes = [];

                    foreach ($module->getDataSourcePrefixes($this->getDataSourceKey()) as $prefixes) {
                        $tablePrefixes += $prefixes;
                    }

                    $columns[$columnName]['fieldName'] = Helper_Model::getFieldNameByColumnName(
                        $columnName,
                        $data,
                        $tablePrefixes
                    );

                    foreach (Model::getConfig()->gets('schemeColumnPlugins') as $columnPluginClass) {
                        $columns[$columnName][$columnPluginClass] =
                            $columnPluginClass::schemeColumnPlugin($columnName, $data);
                    }
                }
            }
        }

        return $tables;
    }

    /**
     * Get indexes of table
     *
     * @param  $tableName
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function getIndexes($tableName)
    {
        $dataProvider = $this->getSourceDataProvider();
        $dataProvider->setScheme('information_schema');

        $constraints = [
            'PRIMARY KEY' => [
                'PRIMARY' => []
            ],
            'FOREIGN KEY' => [],
            'UNIQUE' => []
        ];

        $key = ['TABLE_CONSTRAINTS:TABLE_SCHEMA/' . $this->scheme, 'TABLE_CONSTRAINTS:TABLE_NAME/' . $tableName];

        foreach ($dataProvider->get($key) as $constraint) {
            $constraints[$constraint['CONSTRAINT_TYPE']][$constraint['CONSTRAINT_NAME']] = [];
        }

        $key = ['STATISTICS:TABLE_SCHEMA/' . $this->scheme, 'STATISTICS:TABLE_NAME/' . $tableName];

        $indexes = $dataProvider->get($key);

        foreach ($constraints['PRIMARY KEY'] as $constraintName => &$constraint) {
            foreach ($indexes as $index) {
                if ($index['INDEX_NAME'] != $constraintName) {
                    continue;
                }

                $constraint[$index['SEQ_IN_INDEX']] = $index['COLUMN_NAME'];
                unset($index['COLUMN_NAME']);
            }
        }

        foreach ($constraints['UNIQUE'] as $constraintName => &$constraint) {
            foreach ($indexes as $index) {
                if ($index['INDEX_NAME'] != $constraintName) {
                    continue;
                }

                $constraint[$index['SEQ_IN_INDEX']] = $index['COLUMN_NAME'];
                unset($index['COLUMN_NAME']);
            }
        }

        $flippedIndexes = Arrays::column($indexes, 'INDEX_NAME', 'COLUMN_NAME');

        $foreignKeys = [];

        $key = ['KEY_COLUMN_USAGE:TABLE_SCHEMA/' . $this->scheme, 'KEY_COLUMN_USAGE:TABLE_NAME/' . $tableName];

        $referenceColumns = Arrays::column($dataProvider->get($key), 'COLUMN_NAME', 'CONSTRAINT_NAME');

        foreach (array_keys($constraints['FOREIGN KEY']) as $constraintName) {
            $columnName = $referenceColumns[$constraintName];
            $foreignKeys[$flippedIndexes[$columnName]][$constraintName] = $columnName;
        }

        $constraints['FOREIGN KEY'] = $foreignKeys;

        return $constraints;
    }

    /**
     * Get table references from source
     *
     * @param  $tableName
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.6
     */
    public function getReferences($tableName)
    {
        $dataProvider = $this->getSourceDataProvider();
        $dataProvider->setScheme('information_schema');

        $references = [];

        $key = ['REFERENTIAL_CONSTRAINTS:CONSTRAINT_SCHEMA/' . $this->scheme, 'CONSTRAINTS:TABLE_NAME/' . $tableName];

        foreach ($dataProvider->get($key) as $reference) {
            $references[$reference['REFERENCED_TABLE_NAME']] = [
                'constraintName' => $reference['CONSTRAINT_NAME'],
                'onUpdate' => $reference['UPDATE_RULE'],
                'onDelete' => $reference['DELETE_RULE'],
            ];
        }

        return $references;
    }

    /**
     * Get table scheme from source
     *
     * @param  $tableName
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function getColumns($tableName)
    {
        $columns = [];

        $dataProvider = $this->getSourceDataProvider();
        $dataProvider->setScheme('information_schema');

        $key = ['COLUMNS:TABLE_SCHEMA/' . $this->scheme, 'COLUMNS:TABLE_NAME/' . $tableName];

        foreach ($dataProvider->get($key) as $column) {
            $columnName = $column['COLUMN_NAME'];
            $default = $default = $column['COLUMN_DEFAULT'];

            if (empty($default) && strstr($columnName, '__json') == '__json') {
                $default = '[]';
            }

            $length = !isset($column['CHARACTER_MAXIMUM_LENGTH'])
                ? (!isset($column['DATETIME_PRECISION'])
                    ? $column['NUMERIC_PRECISION'] . ',' . $column['NUMERIC_SCALE']
                    : $column['DATETIME_PRECISION'])
                : $column['CHARACTER_MAXIMUM_LENGTH'];

            $columns[$columnName] = [
                'extra' => $column['EXTRA'],
                'type' => $column['COLUMN_TYPE'],
                'dataType' => $column['DATA_TYPE'],
                'length' => $length,
                'characterSet' => $column['CHARACTER_SET_NAME'],
                'nullable' => $column['IS_NULLABLE'] == 'YES',
                'default' => $default,
                'comment' => $column['COLUMN_COMMENT']
            ];
        }

        return $columns;
    }

    /**
     * Execute query create table to data source
     *
     * @param  Query $query
     * @return array
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since   0.2
     */
    public function executeCreate(Query $query)
    {
        $data = [];

        $statement = $this->getStatement($query->getBody(), $query->getBinds());

        $logger = Logger::getInstance(__CLASS__);

        if ($statement->execute() === false) {
            $errno = $statement->errno;
            $error = $statement->error;
            $statement->close();

            $logger->exception(
                [
                    '#' . $errno . ': {$0} - {$1} [{$2}]',
                    [$error, print_r($query->getBody(), true), implode(', ', $query->getBinds())]
                ],
                __FILE__,
                __LINE__
            );
        }

        $data[Query_Result::AFFECTED_ROWS] = $statement->affected_rows;

        $statement->close();

        return $data;
    }

    /**
     * Execute query drop table to data source
     *
     * @param  Query $query
     * @return array
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since   0.2
     */
    public function executeDrop(Query $query)
    {
        $data = [];

        $statement = $this->getStatement($query->getBody(), $query->getBinds());

        $logger = Logger::getInstance(__CLASS__);

        if ($statement->execute() === false) {
            $errno = $statement->errno;
            $error = $statement->error;
            $statement->close();

            $logger->exception(
                [
                    '#' . $errno . ': {$0} - {$1} [{$2}]',
                    [$error, print_r($query->getBody(), true), implode(', ', $query->getBinds())]
                ],
                __FILE__,
                __LINE__
            );
        }

        $data[Query_Result::AFFECTED_ROWS] = $statement->affected_rows;

        $statement->close();

        return $data;
    }

    /**
     * Return data provider class
     *
     * @return Data_Provider
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
     */
    public function getDataProviderClass()
    {
        return Mysqli::DATA_PROVIDER_CLASS;
    }

    /**
     * Return query translator class
     *
     * @return Query_Translator
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
     */
    public function getQueryTranslatorClass()
    {
        return Mysqli::QUERY_TRANSLATOR_CLASS;
    }

    /**
     * Begin transaction
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   0.5
     * @param string $isolationLevel SET TRANSACTION ISOLATION LEVEL (REPEATABLE READ|READ COMMITTED|READ UNCOMMITTED|SERIALIZABLE)
     */
    public function beginTransaction($isolationLevel = null)
    {
        if ($this->savePointLevel === null) {
            $this->savePointLevel = 0;

            if ($isolationLevel) {
                $this->executeNativeQuery('SET TRANSACTION ISOLATION LEVEL ' . $isolationLevel);
            }

            $this->getConnection()->autocommit(false);
            Logger::log('false', 'mysql autocommit', 'INFO');

//            $this->getConnection()->begin_transaction(0, 'level_' . $this->savePointLevel);
            Logger::log('level_' . $this->savePointLevel, 'mysql transaction', 'INFO');
        } else {
            $this->savePointLevel++;

            $this->savePoint('transaction');
        }
    }

    /**
     * Commit transaction
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   0.5
     */
    public function commitTransaction()
    {
        if ($this->savePointLevel === 0) {
            $this->getConnection()->commit();
            Logger::log('level_' . $this->savePointLevel, 'mysql commit', 'INFO');

            $this->savePointLevel = null;

            $this->getConnection()->autocommit(true);
            Logger::log('true', 'mysql autocommit', 'INFO');

            $this->executeNativeQuery('SET TRANSACTION ISOLATION LEVEL ' . Mysqli::TRANSACTION_REPEATABLE_READ);
        } else {
            $this->releaseSavePoint('transaction');

            $this->savePointLevel--;
        }
    }

    /**
     * Rollback transaction

     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   0.5
     */
    public function rollbackTransaction()
    {
        if ($this->savePointLevel === 0) {
                $this->getConnection()->rollback();
                Logger::log('level_' . $this->savePointLevel, 'mysql rollback', 'INFO');

            $this->savePointLevel = null;

            $this->getConnection()->autocommit(true);
            Logger::log('true', 'mysql autocommit', 'INFO');

            $this->executeNativeQuery('SET TRANSACTION ISOLATION LEVEL ' . Mysqli::TRANSACTION_REPEATABLE_READ);
        } else {
            $this->rollbackSavePoint('transaction');

            $this->savePointLevel--;
        }
    }

    /**
     * Create save point transactions
     *
     * @param $savePoint
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   1.1
     */
    public function savePoint($savePoint)
    {
        $this->executeNativeQuery('SAVEPOINT ' . 'level_' . $this->savePointLevel . '_' . $savePoint);
    }

    /**
     * Rollback save point transactions
     *
     * @param $savePoint
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   1.1
     */
    public function rollbackSavePoint($savePoint)
    {
        $this->executeNativeQuery('ROLLBACK TO SAVEPOINT ' . 'level_' . $this->savePointLevel . '_' . $savePoint);
    }

    /**
     * Release save point transactions
     *
     * @param $savePoint
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   1.1
     */
    public function releaseSavePoint($savePoint)
    {
        $this->executeNativeQuery('RELEASE SAVEPOINT ' . 'level_' . $this->savePointLevel . '_' . $savePoint);
    }

    /**
     * Execute native query
     *
     * @param string $query
     * @return Query_Result
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   1.1
     */
    public function executeNativeQuery($query)
    {
        $logger = Logger::getInstance(__CLASS__);

        if ($this->getConnection()->query($query) === false) {
            $errno = $this->getConnection()->errno;
            $error = $this->getConnection()->error;

            Logger::log(
                '#' . $errno . ': ' . $error . ' - ' . $query,
                'native query (error)',
                'ERROR'
            );
            $logger->exception(['#' . $errno . ': {$0} - {$1}', [$error, $query]], __FILE__, __LINE__);
        } else {
            Logger::log($query, 'native query (new)', 'INFO');
        }
    }
}
