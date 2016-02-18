<?php
/**
 * Ice data source implementation mysqli class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\DataSource;

use Ice\Core\Converter;
use Ice\Core\DataProvider;
use Ice\Core\DataSource;
use Ice\Core\Debuger;
use Ice\Core\Exception;
use Ice\Core\Logger;
use Ice\Core\Model;
use Ice\Core\Module;
use Ice\Core\Profiler;
use Ice\Core\Query;
use Ice\Core\QueryBuilder;
use Ice\Core\QueryResult;
use Ice\Core\QueryTranslator;
use Ice\Exception\DataSource_Insert;
use Ice\Exception\DataSource_Insert_DuplicateEntry;
use Ice\Exception\DataSource_Select_Error;
use Ice\Exception\DataSource_Statement_Error;
use Ice\Exception\DataSource_Statement_TableNotFound;
use Ice\Exception\DataSource_Statement_UnknownColumn;
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
 * @see Ice\Core\DataSource
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage DataSource
 */
class Mysqli extends DataSource
{
    const DATA_PROVIDER_CLASS = 'Ice\DataProvider\Mysqli';
    const QUERY_TRANSLATOR_CLASS = 'Ice\QueryTranslator\Sql';

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

        $data[QueryResult::ROWS] = [];

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

//            $data[QueryResult::ROWS][] = $row;

            $id = implode('_', array_intersect_key($row, array_flip($pkFieldNames)));

            if ($id) {
                $data[QueryResult::ROWS][$id] = $row;
            } else {
                $data[QueryResult::ROWS][] = $row;
            }
        }

        $data[QueryResult::NUM_ROWS] = count($data[QueryResult::ROWS]);

        if ($result->num_rows != $data[QueryResult::NUM_ROWS]) {
            throw new DataSource_Select_Error('Real selected rows not equal result num rows: duplicate primary key');
        }

        $result->free_result();
        $statement->free_result();
        $statement->close();

        unset($result);
        unset($statement);

        if ($query->isCalcFoundRows()) {
            $result = $this->getConnection()->query('SELECT FOUND_ROWS()');
            $foundRows = $result->fetch_row();
            $result->close();
            $data[QueryResult::FOUND_ROWS] = reset($foundRows);
        } else {
            $data[QueryResult::FOUND_ROWS] = $data[QueryResult::NUM_ROWS];
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
                    $exceptionClass = DataSource_Statement_TableNotFound::class;
                    break;
                case 1054:
                    $exceptionClass = DataSource_Statement_UnknownColumn::class;
                    break;
                default:
                    $exceptionClass = DataSource_Statement_Error::class;
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

        foreach ($query->getBindParts()[QueryBuilder::PART_VALUES] as $row) {
            if ($pkFieldName) {
                if (!isset($row[$pkFieldName])) {
                    $row[$pkFieldName] = $insertId;
                    $insertId++;
                } else {
                    $insertId = null;
                }
            }

            $insertKeys = array_intersect_key($row, array_flip($pkFieldNames));

            $data[QueryResult::INSERT_ID][] = $insertKeys;
            $data[QueryResult::ROWS][implode('_', $insertKeys)] = $row;
        }

        $data[QueryResult::AFFECTED_ROWS] = $statement->affected_rows;

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

        foreach ($query->getBindParts()[QueryBuilder::PART_SET] as $row) {
            $insertKey = implode('_', array_intersect_key($row, array_flip($pkFieldNames)));
            $data[QueryResult::ROWS][$insertKey] = $row;
        }

        $data[QueryResult::AFFECTED_ROWS] = $statement->affected_rows;

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

        $data[QueryResult::AFFECTED_ROWS] = $statement->affected_rows;

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

        $iceql = ['information_schema.TABLES/TABLE_NAME,ENGINE,TABLE_COLLATION,TABLE_COMMENT' => 'TABLE_SCHEMA:' . $this->scheme];

        foreach ($this->get($iceql) as $table) {
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
                                if (!isset($tables[$tableName1]['relations']['manyToMany'][$tableName2])) {
                                    $tables[$tableName1]['relations']['manyToMany'][$tableName2] = [];
                                }

                                $tables[$tableName1]['relations']['manyToMany'][$tableName2][] = $table['TABLE_NAME'];
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
        $constraints = [
            'PRIMARY KEY' => [
                'PRIMARY' => []
            ],
            'FOREIGN KEY' => [],
            'UNIQUE' => []
        ];

        $iceql = ['information_schema.TABLE_CONSTRAINTS/CONSTRAINT_TYPE,CONSTRAINT_NAME' => 'TABLE_SCHEMA:' . $this->scheme . ',TABLE_NAME:' . $tableName];

        foreach ($this->get($iceql) as $constraint) {
            $constraints[$constraint['CONSTRAINT_TYPE']][$constraint['CONSTRAINT_NAME']] = [];
        }

        $iceql = ['information_schema.STATISTICS/INDEX_NAME,SEQ_IN_INDEX,COLUMN_NAME' => 'TABLE_SCHEMA:' . $this->scheme . ',TABLE_NAME:' . $tableName];

        $indexes = $this->get($iceql);

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

        $iceql = ['information_schema.KEY_COLUMN_USAGE/COLUMN_NAME,CONSTRAINT_NAME' => 'TABLE_SCHEMA:' . $this->scheme . ',TABLE_NAME:' . $tableName];

        $referenceColumns = Arrays::column($this->get($iceql), 'COLUMN_NAME', 'CONSTRAINT_NAME');

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
        $references = [];

        $iceql = ['information_schema.REFERENTIAL_CONSTRAINTS/REFERENCED_TABLE_NAME,CONSTRAINT_NAME,UPDATE_RULE,DELETE_RULE' => 'CONSTRAINT_SCHEMA:' . $this->scheme . ',TABLE_NAME:' . $tableName];

        foreach ($this->get($iceql) as $reference) {
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

        $iceql = ['information_schema.COLUMNS/COLUMN_NAME,COLUMN_DEFAULT,CHARACTER_MAXIMUM_LENGTH,DATETIME_PRECISION,NUMERIC_PRECISION,NUMERIC_SCALE,EXTRA,COLUMN_TYPE,DATA_TYPE,CHARACTER_SET_NAME,IS_NULLABLE,COLUMN_COMMENT' => 'TABLE_SCHEMA:' . $this->scheme . ',TABLE_NAME:' . $tableName];

        foreach ($this->get($iceql) as $column) {
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

        $data[QueryResult::AFFECTED_ROWS] = $statement->affected_rows;

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

        $data[QueryResult::AFFECTED_ROWS] = $statement->affected_rows;

        $statement->close();

        return $data;
    }

    /**
     * Return data provider class
     *
     * @return DataProvider
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
     * @return QueryTranslator
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
     * @param string $sql
     * @return QueryResult
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   1.1
     */
    public function executeNativeQuery($sql)
    {
        $result = $this->query($sql);

        $data[QueryResult::ROWS] = [];

        if (is_object($result)) {
            if ($result->num_rows) {
                $data[QueryResult::ROWS] = $result->fetch_all(MYSQLI_ASSOC);
            }

            $result->free_result();
        }

        $data[QueryResult::NUM_ROWS] = count($data[QueryResult::ROWS]);
        $data[QueryResult::FOUND_ROWS] = $data[QueryResult::NUM_ROWS];

        return $data;
    }

    /**
     * @param $sql
     * @return mixed
     * @throws Exception
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   1.1
     */
    public function query($sql)
    {
        $startTime = Profiler::getMicrotime();
        $startMemory = Profiler::getMemoryGetUsage();

        $connection = $this->getSourceDataProvider()->getConnection();

        $result = $connection->query($sql);

        if ($result === false) {
            $errno = $this->getConnection()->errno;
            $error = $this->getConnection()->error;

            Logger::log(
                '#' . $errno . ': ' . $error . ' - ' . $sql,
                'query (error)',
                'ERROR'
            );

            Logger::getInstance(__CLASS__)
                ->exception(['#' . $errno . ': {$0} - {$1}', [$error, $sql]], __FILE__, __LINE__);
        }

        Profiler::setPoint($sql, $startTime, $startMemory);
        Logger::log(Profiler::getReport($sql), 'query (native)', 'INFO');

        return $result;
    }

    /**
     * Translate ice query language for get data
     *
     * @param array $iceql
     * @return mixed
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   1.1
     */
    public function translateGet(array $iceql)
    {
        $tables = [];

        foreach ($iceql as $tablePart => $wherePart) {
            if (is_int($tablePart)) {
                $tablePart = $wherePart;
                $wherePart = [];
            }

            list($table, $columnsPart) = explode('/', $tablePart);

            if ($pos = strpos($table, ':')) {
                $tableAlias = substr($table, $pos + 1);
                $table = substr($table, 0, $pos);
            } else {
                $tableAlias = String::getRandomString(3, 1);
            }

            $join = null;

            if (strpos($tableAlias, '=')) {
                $join = $tableAlias;
                $tableAlias = substr($tableAlias, 0, strpos($tableAlias, '.'));
            }

            $tables[$tableAlias] = [
                'table' => $table,
                'join' => $join
            ];

            $columns = [];

            foreach (explode(',', $columnsPart) as $column) {
                $columnAlias = null;

                if ($pos = strpos($column, ':')) {
                    $columnAlias = substr($column, $pos + 1);
                    $column = substr($column, 0, $pos);
                }

                $modifiers = explode('|', $column);

                $column = array_shift($modifiers);

                if (!$columnAlias) {
                    $columnAlias = $column;
                }

                $columns[$columnAlias] = [
                    'column' => $column,
                    'order' => in_array('ASC', $modifiers) ? 'ASC' : (in_array('DESC', $modifiers) ? 'DESC' : null),
                    'group' => in_array('GROUP', $modifiers)
                ];
            }

            unset($column);

            $tables[$tableAlias]['columns'] = $columns;

            $wheres = [];

            foreach (explode(',', $wherePart) as $where) {
                list($where, $value) = explode(':', $where);

                $whereModifiers = explode('|', $where);
                $where = array_shift($whereModifiers);

                $valueModifiers = explode('|', $value);

                $wheres[$where] = [
                    'value' => array_shift($valueModifiers),
                    'condition' => in_array('OR', $whereModifiers) ? 'OR' : 'AND',
                    'operator' => in_array('LIKE', $valueModifiers) ? 'LIKE' : (in_array('<>', $valueModifiers) ? '<>' : '='),
                ];
            }

            $tables[$tableAlias]['where'] = $wheres;
        }

        $sql = "\n" . 'SELECT';

        $isFirst = true;

        foreach ($tables as $tableAlias => $table) {
            foreach ($table['columns'] as $columnAlias => $column) {
                if ($isFirst) {
                    $isFirst = false;
                } else {
                    $sql .= ',';
                }

                $sql .= "\n\t`" . $tableAlias . '`.`' . $column['column'] . '`';

                if ($column['column'] != $columnAlias) {
                    $sql .= ' AS `' . $columnAlias . '`';
                }
            }
        }

        $isWhere = false;

        foreach ($tables as $tableAlias => $table) {
            $sql .= $table['join']
                ? "\n" . 'LEFT JOIN ' . $table['table'] . ' `' . $tableAlias . '` ON (' . $table['join'] . ')'
                : "\n" . 'FROM ' . $table['table'] . ' `' . $tableAlias . '`';

            if (!empty($table['where'])) {
                $isWhere = true;
            }
        }

        if ($isWhere) {
            $sql .= "\n" . 'WHERE';

            $isFirst = true;

            foreach ($tables as $tableAlias => $table) {
                foreach ($table['where'] as $where => $value) {
                    if ($isFirst) {
                        $sql .= "\n\t`" . $tableAlias . '`.`' . $where . '`' . $value['operator'] . '"' . $value['value'] . '"';
                        $isFirst = false;
                    } else {
                        $sql .= "\n\t" . $value['condition'] . ' `' . $tableAlias . '`.`' . $where . '`' . $value['operator'] . '"' . $value['value'] . '"';
                    }
                }
            }
        }

        return $sql . "\n";
    }

    /**
     * Translate ice query language for set data
     *
     * @param array $iceql
     * @return mixed setted value
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   1.1
     */
    public function translateSet(array $iceql)
    {
        $tables = [];

        foreach ($iceql as $tablePart => $wherePart) {
            if (is_int($tablePart)) {
                $tablePart = $wherePart;
                $wherePart = [];
            }

            list($table, $columnsPart) = explode('/', $tablePart);

            if ($pos = strpos($table, ':')) {
                $tableAlias = substr($table, $pos + 1);
                $table = substr($table, 0, $pos);
            } else {
                $tableAlias = String::getRandomString(3, 1);
            }

            $join = null;

            if (strpos($tableAlias, '=')) {
                $join = $tableAlias;
                $tableAlias = substr($tableAlias, 0, strpos($tableAlias, '.'));
            }

            $tables[$tableAlias] = [
                'table' => $table,
                'join' => $join
            ];

            $columns = [];

            foreach (explode(',', $columnsPart) as $column) {
                $columnAlias = null;

                if ($pos = strpos($column, ':')) {
                    $columnAlias = substr($column, $pos + 1);
                    $column = substr($column, 0, $pos);
                }

                $modifiers = explode('|', $column);

                $column = array_shift($modifiers);

                if (!$columnAlias) {
                    $columnAlias = $column;
                }

                $columns[$columnAlias] = [
                    'column' => $column,
                    'order' => in_array('ASC', $modifiers) ? 'ASC' : (in_array('DESC', $modifiers) ? 'DESC' : null),
                    'group' => in_array('GROUP', $modifiers)
                ];
            }

            unset($column);

            $tables[$tableAlias]['columns'] = $columns;

            $wheres = [];

            foreach (explode(',', $wherePart) as $where) {
                list($where, $value) = explode(':', $where);

                $whereModifiers = explode('|', $where);
                $where = array_shift($whereModifiers);

                $valueModifiers = explode('|', $value);

                $wheres[$where] = [
                    'value' => array_shift($valueModifiers),
                    'condition' => in_array('OR', $whereModifiers) ? 'OR' : 'AND',
                    'operator' => in_array('LIKE', $valueModifiers) ? 'LIKE' : (in_array('<>', $valueModifiers) ? '<>' : '='),
                ];
            }

            $tables[$tableAlias]['where'] = $wheres;
        }

        $sql = "\n" . 'SELECT';

        $isFirst = true;

        foreach ($tables as $tableAlias => $table) {
            foreach ($table['columns'] as $columnAlias => $column) {
                if ($isFirst) {
                    $isFirst = false;
                } else {
                    $sql .= ',';
                }

                $sql .= "\n\t`" . $tableAlias . '`.`' . $column['column'] . '`';

                if ($column['column'] != $columnAlias) {
                    $sql .= ' AS `' . $columnAlias . '`';
                }
            }
        }

        $isWhere = false;

        foreach ($tables as $tableAlias => $table) {
            $sql .= $table['join']
                ? "\n" . 'LEFT JOIN ' . $table['table'] . ' `' . $tableAlias . '` ON (' . $table['join'] . ')'
                : "\n" . 'FROM ' . $table['table'] . ' `' . $tableAlias . '`';

            if (!empty($table['where'])) {
                $isWhere = true;
            }
        }

        if ($isWhere) {
            $sql .= "\n" . 'WHERE';

            $isFirst = true;

            foreach ($tables as $tableAlias => $table) {
                foreach ($table['where'] as $where => $value) {
                    if ($isFirst) {
                        $sql .= "\n\t`" . $tableAlias . '`.`' . $where . '`' . $value['operator'] . '"' . $value['value'] . '"';
                        $isFirst = false;
                    } else {
                        $sql .= "\n\t" . $value['condition'] . ' `' . $tableAlias . '`.`' . $where . '`' . $value['operator'] . '"' . $value['value'] . '"';
                    }
                }
            }
        }

        Debuger::dump($iceql);
        Debuger::dump($tables);
        Debuger::dump($sql);

        return $sql . "\n";
    }

    /**
     * Translate ice query language for delete data
     *
     * @param array $iceql
     * @return bool|mixed
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   1.1
     */
    public function translateDelete(array $iceql)
    {
        // TODO: Implement translateDelete() method.
    }
}
