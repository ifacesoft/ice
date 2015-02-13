<?php
/**
 * Ice data source implementation mysqli class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Data\Source;

use Ice\Core\Data_Provider;
use Ice\Core\Data_Source;
use Ice\Core\Exception;
use Ice\Core\Logger;
use Ice\Core\Model;
use Ice\Core\Query;
use Ice\Core\Query_Builder;
use Ice\Core\Query_Result;
use Ice\Core\Query_Translator;
use Ice\Helper\Arrays;
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
 * @package Ice
 * @subpackage Data_Source
 */
class Mysqli extends Data_Source
{
    const DATA_PROVIDER_CLASS = 'Ice\Data\Provider\Mysqli';
    const QUERY_TRANSLATOR_CLASS = 'Ice\Query\Translator\Sql';

    /**
     * Execute query select to data source
     *
     * @param Query $query
     * @return array
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.0
     */
    public function executeSelect(Query $query)
    {
        $data = [];

        $queryTranslatorClass = $this->getQueryTranslatorClass();
        $data[Query_Result::QUERY_BODY] = $queryTranslatorClass::getInstance()->translate($query->getBodyParts());
        $data[Query_Result::QUERY_PARAMS] = $query->getBinds();

        $statement = $this->getStatement($data[Query_Result::QUERY_BODY], $data[Query_Result::QUERY_PARAMS]);

        if ($statement->execute() === false) {
            $errno = $statement->errno;
            $error = $statement->error;
            $statement->close();

            Data_Source::getLogger()->exception(
                [
                    '#' . $errno . ': {$0} - {$1} [{$2}]',
                    [$error, print_r($data[Query_Result::QUERY_BODY], true), implode(', ', $data[Query_Result::QUERY_PARAMS])]
                ],
                __FILE__, __LINE__
            );
        }
//            $statement->store_result(); // Так почемуто не работает
        /** @var mysqli_result $result */
        $result = $statement->get_result();

        if ($result === false) {
            $errno = $statement->errno;
            $error = $statement->error;
            $statement->close();

            Data_Source::getLogger()->exception(
                [
                    '#' . $errno . ': {$0} - {$1} [{$2}]',
                    [$error, print_r($data[Query_Result::QUERY_BODY], true), implode(', ', $data[Query_Result::QUERY_PARAMS])]
                ],
                __FILE__, __LINE__
            );
        }

        /** @var Model $modelClass */
        $modelClass = $query->getModelClass();

        $pkFieldNames = $modelClass::getPkFieldNames();

        $data[Query_Result::NUM_ROWS] = $result->num_rows;

        while ($row = $result->fetch_assoc()) {
            $data[Query_Result::ROWS][implode('_', array_intersect_key($row, array_flip($pkFieldNames)))] = $row;
        }

        $result->close();
        $statement->free_result();
        $statement->close();

        if ($query->isCalcFoundRows()) {
            $result = $this->getConnection()->query('SELECT FOUND_ROWS()');
            $foundRows = $result->fetch_row();
            $result->close();
            $query->setPagination(reset($foundRows));
        } else {
            $query->setPagination($data[Query_Result::NUM_ROWS]);
        }

        $data[Query_Result::QUERY] = $query;

        return $data;
    }

    /**
     * Get connection instance
     *
     * @param string|null $scheme
     * @return \Mysqli
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getConnection($scheme = null)
    {
        return parent::getConnection();
    }

    /**
     * Prepare query statement for query
     *
     * @param $body
     * @param array $binds
     * @return mysqli_stmt
     * @throws Exception
     * @internal param array $bodyParts
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.0
     */
    public function getStatement($body, array $binds)
    {
        $statement = $this->getConnection()->prepare($body);

        if (!$statement) {
            Data_Source::getLogger()->exception(['#' . $this->getConnection()->errno . ': {$0}', $this->getConnection()->error], __FILE__, __LINE__);
        }

        $types = '';
        foreach ($binds as $bind) {
            $types .= gettype($bind)[0];
        }

        $values = [str_replace('N', 's', $types)];

        if (!empty($types)) {
            $values = array_merge($values, $binds);

            if (call_user_func_array(array($statement, 'bind_param'), $this->makeValuesReferenced($values)) === false) {
                Mysqli::getLogger()->exception('Bind params failure', __FILE__, __LINE__, null, $types);
            }
        }

        return $statement;
    }

    /**
     * Override values in query binds (man solution)
     *
     * @param $arr
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
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
     * @param Query $query
     * @return array
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.0
     */
    public function executeInsert(Query $query)
    {
        $data = [];

        $queryTranslatorClass = $this->getQueryTranslatorClass();
        $data[Query_Result::QUERY_BODY] = $queryTranslatorClass::getInstance()->translate($query->getBodyParts());
        $data[Query_Result::QUERY_PARAMS] = $query->getBinds();

        $statement = $this->getStatement($data[Query_Result::QUERY_BODY], $data[Query_Result::QUERY_PARAMS]);

        if ($statement->execute() === false) {
            $errno = $statement->errno;
            $error = $statement->error;
            $statement->close();

            Data_Source::getLogger()->exception(
                [
                    '#' . $errno . ': {$0} - {$1} [{$2}]',
                    [$error, print_r($data[Query_Result::QUERY_BODY], true), implode(', ', $data[Query_Result::QUERY_PARAMS])]
                ],
                __FILE__, __LINE__
            );
        }

        /** @var Model $modelClass */
        $modelClass = $query->getModelClass();
        $pkFieldNames = $modelClass::getPkFieldNames();

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

        $data[Query_Result::QUERY] = $query;

        return $data;
    }

    /**
     * Execute query update to data source
     *
     * @param Query $query
     * @return array
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.0
     */
    public function executeUpdate(Query $query)
    {
        $data = [];

        $queryTranslatorClass = $this->getQueryTranslatorClass();
        $data[Query_Result::QUERY_BODY] = $queryTranslatorClass::getInstance()->translate($query->getBodyParts());
        $data[Query_Result::QUERY_PARAMS] = $query->getBinds();

        $statement = $this->getStatement($data[Query_Result::QUERY_BODY], $data[Query_Result::QUERY_PARAMS]);

        if ($statement->execute() === false) {
            $errno = $statement->errno;
            $error = $statement->error;
            $statement->close();

            Data_Source::getLogger()->exception(
                [
                    '#' . $errno . ': {$0} - {$1} [{$2}]',
                    [$error, print_r($data[Query_Result::QUERY_BODY], true), implode(', ', $data[Query_Result::QUERY_PARAMS])]
                ],
                __FILE__, __LINE__
            );
        }

        /** @var Model $modelclass */
        $modelclass = $query->getModelClass();
        $pkFieldNames = $modelclass::getPkFieldNames();

        foreach ($query->getBindParts()[Query_Builder::PART_SET] as $row) {
            $insertKey = implode('_', array_intersect_key($row, array_flip($pkFieldNames)));
            $data[Query_Result::ROWS][$insertKey] = $row;
        }

        $data[Query_Result::AFFECTED_ROWS] = $statement->affected_rows;

        $statement->close();

        $data[Query_Result::QUERY] = $query;

        return $data;
    }

    /**
     * Execute query update to data source
     *
     * @param Query $query
     * @return array
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.0
     */
    public function executeDelete(Query $query)
    {
        $data = [];

        $queryTranslatorClass = $this->getQueryTranslatorClass();
        $data[Query_Result::QUERY_BODY] = $queryTranslatorClass::getInstance()->translate($query->getBodyParts());
        $data[Query_Result::QUERY_PARAMS] = $query->getBinds();

        $statement = $this->getStatement($data[Query_Result::QUERY_BODY], $data[Query_Result::QUERY_PARAMS]);

        if ($statement->execute() === false) {
            $errno = $statement->errno;
            $error = $statement->error;
            $statement->close();

            Data_Source::getLogger()->exception(
                [
                    '#' . $errno . ': {$0} - {$1} [{$2}]',
                    [$error, print_r($data[Query_Result::QUERY_BODY], true), implode(', ', $data[Query_Result::QUERY_PARAMS])]
                ],
                __FILE__, __LINE__
            );
        }

        $data[Query_Result::AFFECTED_ROWS] = $statement->affected_rows;

        $statement->close();

        $data[Query_Result::QUERY] = $query;

        return $data;
    }

    /**
     * Get indexes of table
     *
     * @param $tableName
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
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

        foreach ($dataProvider->get(['TABLE_CONSTRAINTS:TABLE_SCHEMA/' . $this->_scheme, 'TABLE_CONSTRAINTS:TABLE_NAME/' . $tableName]) as $constraint) {
            $constraints[$constraint['CONSTRAINT_TYPE']][$constraint['CONSTRAINT_NAME']] = [];
        }

        $indexes = $dataProvider->get(['STATISTICS:TABLE_SCHEMA/' . $this->_scheme, 'STATISTICS:TABLE_NAME/' . $tableName]);

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

        $referenceColumns = Arrays::column(
            $dataProvider->get(['KEY_COLUMN_USAGE:TABLE_SCHEMA/' . $this->_scheme, 'KEY_COLUMN_USAGE:TABLE_NAME/' . $tableName]),
            'COLUMN_NAME',
            'CONSTRAINT_NAME'
        );

        foreach (array_keys($constraints['FOREIGN KEY']) as $constraintName) {
            $columnName = $referenceColumns[$constraintName];
            $foreignKeys[$flippedIndexes[$columnName]][$constraintName] = $columnName;
        }

        $constraints['FOREIGN KEY'] = $foreignKeys;

        return $constraints;
    }

    /**
     * Get data Scheme from data source
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getTables()
    {
        $tables = [];

        $dataProvider = $this->getSourceDataProvider();
        $dataProvider->setScheme('information_schema');

        foreach ($dataProvider->get('TABLES:TABLE_SCHEMA/' . $this->_scheme) as $table) {
            $tables[$table['TABLE_NAME']] = [
                'engine' => $table['ENGINE'],
                'charset' => $table['TABLE_COLLATION'],
                'comment' => $table['TABLE_COMMENT']
            ];
        }

        return $tables;
    }

    /**
     * Get table scheme from source
     *
     * @param $tableName
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getColumns($tableName)
    {
        $columns = [];

        $dataProvider = $this->getSourceDataProvider();
        $dataProvider->setScheme('information_schema');

        foreach ($dataProvider->get(['COLUMNS:TABLE_SCHEMA/' . $this->_scheme, 'COLUMNS:TABLE_NAME/' . $tableName]) as $column) {
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
     * @param Query $query
     * @return array
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.2
     */
    public function executeCreate(Query $query)
    {
        $data = [];

        $queryTranslatorClass = $this->getQueryTranslatorClass();
        $data[Query_Result::QUERY_BODY] = $queryTranslatorClass::getInstance()->translate($query->getBodyParts());
        $data[Query_Result::QUERY_PARAMS] = $query->getBinds();

        $statement = $this->getStatement($data[Query_Result::QUERY_BODY], $data[Query_Result::QUERY_PARAMS]);

        if ($statement->execute() === false) {
            $errno = $statement->errno;
            $error = $statement->error;
            $statement->close();

            Data_Source::getLogger()->exception(
                [
                    '#' . $errno . ': {$0} - {$1} [{$2}]',
                    [$error, print_r($data[Query_Result::QUERY_BODY], true), implode(', ', $data[Query_Result::QUERY_PARAMS])]
                ],
                __FILE__, __LINE__
            );
        }

        $data[Query_Result::AFFECTED_ROWS] = $statement->affected_rows;

        $statement->close();

        $data[Query_Result::QUERY] = $query;

        return $data;
    }

    /**
     * Execute query drop table to data source
     *
     * @param Query $query
     * @return array
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.2
     */
    public function executeDrop(Query $query)
    {
        $data = [];

        $queryTranslatorClass = $this->getQueryTranslatorClass();
        $data[Query_Result::QUERY_BODY] = $queryTranslatorClass::getInstance()->translate($query->getBodyParts());
        $data[Query_Result::QUERY_PARAMS] = $query->getBinds();

        $statement = $this->getStatement($data[Query_Result::QUERY_BODY], $data[Query_Result::QUERY_PARAMS]);

        if ($statement->execute() === false) {
            $errno = $statement->errno;
            $error = $statement->error;
            $statement->close();

            Data_Source::getLogger()->exception(
                [
                    '#' . $errno . ': {$0} - {$1} [{$2}]',
                    [$error, print_r($data[Query_Result::QUERY_BODY], true), implode(', ', $data[Query_Result::QUERY_PARAMS])]
                ],
                __FILE__, __LINE__
            );
        }

        $data[Query_Result::AFFECTED_ROWS] = $statement->affected_rows;

        $statement->close();

        $data[Query_Result::QUERY] = $query;

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
     * @since 0.4
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
     * @since 0.4
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
     * @version 0.5
     * @since 0.5
     */
    public function beginTransaction()
    {
        $this->getConnection()->autocommit(false);
    }

    /**
     * Commit transaction
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.5
     */
    public function commitTransaction()
    {
        $this->getConnection()->commit();
        $this->getConnection()->autocommit(true);
    }

    /**
     * Rollback transaction
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.5
     */
    public function rollbackTransaction()
    {
        $this->getConnection()->rollback();
        $this->getConnection()->autocommit(true);
    }
}