<?php
/**
 * Ice data source implementation mysqli class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Data\Source;

use Ice\Core\Data_Source;
use Ice\Core\Exception;
use Ice\Core\Logger;
use Ice\Core\Model;
use Ice\Core\Query;
use Ice\Core\Query_Builder;
use Ice\Core\Query_Result;
use Ice\Helper\Arrays;
use mysqli_stmt;

/**
 * Class Mysqli
 *
 * Implemets mysqli data source
 *
 * @see Ice\Core\Data_Source
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Data_Source
 *
 * @version 0.1
 * @since 0.0
 */
class Mysqli extends Data_Source
{
    /**
     * Execute query select to data source
     *
     * @param mixed $statement
     * @param Query $query
     * @return array
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.0
     */
    public function executeSelect($statement, Query $query)
    {
        if ($statement->execute() === false) {
            $errno = $statement->errno;
            $error = $statement->error;
            $statement->close();
            Data_Source::getLogger()->fatal(['#' . $errno . ': {$0}', $error], __FILE__, __LINE__);
        }
//            $statement->store_result(); // Так почемуто не работает
        $result = $statement->get_result();

        if ($result === false) {
            $errno = $statement->errno;
            $error = $statement->error;
            $statement->close();
            Data_Source::getLogger()->fatal(['#' . $errno . ': {$0}', $error], __FILE__, __LINE__);
        }

        $data = [];

        /** @var Model $modelClass */
        $modelClass = $query->getModelClass();

        $data[Query_Result::RESULT_MODEL_CLASS] = $modelClass;
        $pkFieldNames = $modelClass::getPkFieldNames();

        $data[Query_Result::NUM_ROWS] = $result->num_rows;

        while ($row = $result->fetch_assoc()) {
            $data[Query_Result::RESULT_ROWS][implode('_', array_intersect_key($row, array_flip($pkFieldNames)))] = $row;
        }

        $result->close();
        $statement->free_result();
        $statement->close();

        if ($query->isCalcFoundRows()) {
            $result = $this->getConnection()->query('SELECT FOUND_ROWS()');
            $foundRows = $result->fetch_row();
            $result->close();
            $data[Query_Result::FOUND_ROWS] = reset($foundRows);
        } else {
            $data[Query_Result::FOUND_ROWS] = $data[Query_Result::NUM_ROWS];
        }

        $limit = $query->getLimit();

        if (!empty($limit)) {
            list($limit, $offset) = $limit;
            $data[Query_Result::LIMIT] = $limit;
            $data[Query_Result::PAGE] = $offset / $limit + 1;
        }

        $data[Query_Result::QUERY_FULL_HASH] = $query->getFullHash();

        return $data;
    }

    /**
     * Prepare query statement for query
     *
     * @param Query $query
     * @throws Exception
     * @return mysqli_stmt
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.0
     */
    public function getStatement(Query $query)
    {
        $statement = $this->getConnection()->prepare($query->getSql());

        if (!$statement) {
            Data_Source::getLogger()->fatal(['#' . $this->getConnection()->errno . ': {$0}', $this->getConnection()->error], __FILE__, __LINE__);
        }

        $binds = $query->getBinds();

        $types = '';
        foreach ($binds as $bind) {
            $types .= gettype($bind)[0];
        }

        $values = [str_replace('N', 's', $types)];

        if (!empty($types)) {
            $values = array_merge($values, $binds);

            if (call_user_func_array(array($statement, 'bind_param'), $this->makeValuesReferenced($values)) === false) {
                Data_Source::getLogger()->fatal('Bind params failure', __FILE__, __LINE__);
            }
        }

        return $statement;
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
     * @param mixed $statement
     * @param Query $query
     * @return array
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.0
     */
    public function executeInsert($statement, Query $query)
    {
        if ($statement->execute() === false) {
            $errno = $statement->errno;
            $error = $statement->error;
            $statement->close();
            Data_Source::getLogger()->fatal(['#' . $errno . ': {$0}', $error], __FILE__, __LINE__);
        }

        $data = [];

        /** @var Model $modelclass */
        $modelclass = $query->getModelClass();
        $pkFieldNames = $modelclass::getPkFieldNames();

        $pkFieldName = count($pkFieldNames) == 1 ? reset($pkFieldNames) : null;

        $data[Query_Result::RESULT_MODEL_CLASS] = $modelclass;

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
            $data[Query_Result::RESULT_ROWS][implode('_', $insertKeys)] = $row;
        }

        $data[Query_Result::AFFECTED_ROWS] = $statement->affected_rows;

        $statement->close();

        return $data;
    }

    /**
     * Execute query update to data source
     *
     * @param mixed $statement
     * @param Query $query
     * @return array
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.0
     */
    public function executeUpdate($statement, Query $query)
    {
        if ($statement->execute() === false) {
            $errno = $statement->errno;
            $error = $statement->error;
            $statement->close();
            Data_Source::getLogger()->fatal(['#' . $errno . ': {$0}', $error], __FILE__, __LINE__);
        }

        $data = [];

        /** @var Model $modelclass */
        $modelclass = $query->getModelClass();
        $pkFieldNames = $modelclass::getPkFieldNames();

        $data[Query_Result::RESULT_MODEL_CLASS] = $modelclass;

        foreach ($query->getBindParts()[Query_Builder::PART_SET] as $row) {
            $insertKey = implode('_', array_intersect_key($row, array_flip($pkFieldNames)));
            $data[Query_Result::RESULT_ROWS][$insertKey] = $row;
        }

        $data[Query_Result::AFFECTED_ROWS] = $statement->affected_rows;

        $statement->close();

        return $data;
    }

    /**
     * Execute query update to data source
     *
     * @param mixed $statement
     * @param Query $query
     * @return array
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.0
     */
    public function executeDelete($statement, Query $query)
    {
        if ($statement->execute() === false) {
            $errno = $statement->errno;
            $error = $statement->error;
            $statement->close();
            Data_Source::getLogger()->fatal(['#' . $errno . ': {$0}', $error], __FILE__, __LINE__);
        }

        $data = [];

        $data[Query_Result::AFFECTED_ROWS] = $statement->affected_rows;

        $statement->close();

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

        foreach ($dataProvider->get(['TABLE_CONSTRAINTS:TABLE_SCHEMA/' . $this->getScheme(), 'TABLE_CONSTRAINTS:TABLE_NAME/' . $tableName]) as $constraint) {
            $constraints[$constraint['CONSTRAINT_TYPE']][$constraint['CONSTRAINT_NAME']] = [];
        }

        $indexes = $dataProvider->get(['STATISTICS:TABLE_SCHEMA/' . $this->getScheme(), 'STATISTICS:TABLE_NAME/' . $tableName]);

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
            $dataProvider->get(['KEY_COLUMN_USAGE:TABLE_SCHEMA/' . $this->getScheme(), 'KEY_COLUMN_USAGE:TABLE_NAME/' . $tableName]),
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

        foreach ($dataProvider->get('TABLES:TABLE_SCHEMA/' . $this->getScheme()) as $table) {
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

        foreach ($dataProvider->get(['COLUMNS:TABLE_SCHEMA/' . $this->getScheme(), 'COLUMNS:TABLE_NAME/' . $tableName]) as $column) {
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
     * @param $statement
     * @param Query $query
     * @return array
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.2
     */
    public function executeCreate($statement, Query $query)
    {
        if ($statement->execute() === false) {
            $errno = $statement->errno;
            $error = $statement->error;
            $statement->close();
            Data_Source::getLogger()->fatal(['#' . $errno . ': {$0}', $error], __FILE__, __LINE__);
        }

        $data = [];

        $data[Query_Result::RESULT_MODEL_CLASS] = $query->getModelClass();
        $data[Query_Result::AFFECTED_ROWS] = $statement->affected_rows;

        $statement->close();

        return $data;
    }

    /**
     * Execute query drop table to data source
     *
     * @param $statement
     * @param Query $query
     * @return array
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.2
     */
    public function executeDrop($statement, Query $query) {
        if ($statement->execute() === false) {
            $errno = $statement->errno;
            $error = $statement->error;
            $statement->close();
            Data_Source::getLogger()->fatal(['#' . $errno . ': {$0}', $error], __FILE__, __LINE__);
        }

        $data = [];

        $data[Query_Result::RESULT_MODEL_CLASS] = $query->getModelClass();
        $data[Query_Result::AFFECTED_ROWS] = $statement->affected_rows;

        $statement->close();

        return $data;
    }
}