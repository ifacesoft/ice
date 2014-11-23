<?php
/**
 * Ice data source implementation mysqli class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Data\Source;

use Ice\Core\Data;
use Ice\Core\Data_Source;
use Ice\Core\Exception;
use Ice\Core\Model;
use Ice\Core\Query;
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
 * @version stable_0
 * @since stable_0
 */
class Mysqli extends Data_Source
{
    /**
     * Execute query select to data source
     *
     * @param Query $query
     * @throws Exception
     * @return array
     */
    public function select(Query $query)
    {
        $statement = $this->getStatement($query);

        if ($statement->execute() === false) {
            $errno = $statement->errno;
            $error = $statement->error;
            $statement->close();
            Data_Source::getLogger()->fatal(['#' . $errno . ': {$0}', $error], __FILE__, __LINE__, null, $query);
        }
//            $statement->store_result(); // Так почемуто не работает
        $result = $statement->get_result();

        if ($result === false) {
            $errno = $statement->errno;
            $error = $statement->error;
            $statement->close();
            Data_Source::getLogger()->fatal(['#' . $errno . ': {$0}', $error], __FILE__, __LINE__, null, $query);
        }

        $data = [];

        /** @var Model $modelclass */
        $modelclass = $query->getModelClass();

        $data[Data::RESULT_MODEL_CLASS] = $modelclass;
        $pkName = $modelclass::getFieldName('/pk');

        $data[DATA::NUM_ROWS] = $result->num_rows;

        while ($row = $result->fetch_assoc()) {
            $data[Data::RESULT_ROWS][$row[$pkName]] = $row;
        }

        $result->close();
        $statement->free_result();
        $statement->close();

        if ($query->isCalcFoundRows()) {
            $result = $this->getConnection()->query('SELECT FOUND_ROWS()');
            $foundRows = $result->fetch_row();
            $result->close();
            $data[Data::FOUND_ROWS] = reset($foundRows);
        } else {
            $data[Data::FOUND_ROWS] = $data[DATA::NUM_ROWS];
        }

        $limit = $query->getLimit();

        if (!empty($limit)) {
            list($limit, $offset) = $limit;
            $data[Data::LIMIT] = $limit;
            $data[Data::PAGE] = $offset / $limit + 1;
        }

        $data[Data::QUERY_FULL_HASH] = $query->getFullHash();

        return $data;
    }

    /**
     * Prepare query statement for query
     *
     * @param Query $query
     * @throws Exception
     * @return mysqli_stmt
     */
    private function getStatement(Query $query)
    {
        $statement = $this->getConnection()->prepare($query->getSql());

        if (!$statement) {
            Data_Source::getLogger()->fatal(['#' . $this->getConnection()->errno . ': {$0}', $this->getConnection()->error], __FILE__, __LINE__, null, $query);
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
                Data_Source::getLogger()->fatal('Bind params failure', __FILE__, __LINE__, null, $query);
            }
        }

        return $statement;
    }

    /**
     * Get connection instance
     *
     * @param string|null $scheme
     * @return \Mysqli
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
     * @throws Exception
     * @return array
     */
    public function insert(Query $query)
    {
        $statement = $this->getStatement($query);

        if ($statement->execute() === false) {
            $errno = $statement->errno;
            $error = $statement->error;
            $statement->close();
            Data_Source::getLogger()->fatal(['#' . $errno . ': {$0}', $error], __FILE__, __LINE__, null, $query);
        }

        $data = [];

        /** @var Model $modelclass */
        $modelclass = $query->getModelClass();

        $rows = $query->getInsertRows(); // TODO: Пока такой костыль.. Не умеет пока проставлять идентификаторы к записяв в момент вставки

        $data[Data::RESULT_MODEL_CLASS] = $modelclass;
        $data[DATA::RESULT_ROWS] = [$statement->insert_id => reset($rows)]; // TODO: Пока только одна запись (см. выше)
        $data[DATA::AFFECTED_ROWS] = $statement->affected_rows;
        $data[DATA::INSERT_ID] = $statement->insert_id;

        if ($data[DATA::AFFECTED_ROWS] == 1) {
            $row = reset($data[DATA::RESULT_ROWS]);
            $row[$modelclass::getFieldName('/pk')] = $data[DATA::INSERT_ID];
            $data[DATA::RESULT_ROWS] = [$row];
        } else {
            throw new Exception('need testing multiinsert in one query');
        }

        $statement->close();

        return $data;
    }

    /**
     * Execute query update to data source
     *
     * @param Query $query
     * @throws Exception
     * @return array
     */
    public function update(Query $query)
    {
        $statement = $this->getStatement($query);

        if ($statement->execute() === false) {
            $errno = $statement->errno;
            $error = $statement->error;
            $statement->close();
            Data_Source::getLogger()->fatal(['#' . $errno . ': {$0}', $error], __FILE__, __LINE__, null, $query);
        }

        $data = [];
//
//        $data[DATA::AFFECTED_ROWS] = $statement->affected_rows;
//        $data[DATA::INSERT_ID] = $statement->insert_id;

        $statement->close();

        return $data;
    }

    /**
     * Execute query update to data source
     *
     * @param Query $query
     * @throws Exception
     * @return array
     */
    public function delete(Query $query)
    {
        $statement = $this->getStatement($query);

        if ($statement->execute() === false) {
            $errno = $statement->errno;
            $error = $statement->error;
            $statement->close();
            Data_Source::getLogger()->fatal(['#' . $errno . ': {$0}', $error], __FILE__, __LINE__, null, $query);
        }

        $data = [];
//
//        $data[DATA::AFFECTED_ROWS] = $statement->affected_rows;
//        $data[DATA::INSERT_ID] = $statement->insert_id;

        $statement->close();

        return $data;
    }

    /**
     * Get indexes of table
     *
     * @param $tableName
     * @return array
     */
    public function getIndexes($tableName) {
        $indexes = [];

        $dataProvider = $this->getSourceDataProvider();
        $dataProvider->setScheme('information_schema');

        foreach ($dataProvider->get(['STATISTICS:TABLE_SCHEMA/' . $this->getScheme(), 'STATISTICS:TABLE_NAME/' . $tableName]) as $index) {
            $indexes[$index['INDEX_NAME']][$index['SEQ_IN_INDEX']] = $index['COLUMN_NAME'];
        }

        foreach ($dataProvider->get(['TABLE_CONSTRAINTS:TABLE_SCHEMA/' . $this->getScheme(), 'TABLE_CONSTRAINTS:TABLE_NAME/' . $tableName]) as $constraint) {
            $indexes[$constraint['CONSTRAINT_TYPE']][$constraint['CONSTRAINT_NAME']] = $indexes[$constraint['CONSTRAINT_NAME']];
            unset($indexes[$constraint['CONSTRAINT_NAME']]);
        }

        return $indexes;
    }

    /**
     * Get data Scheme from data source
     *
     * @return array
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
                'key' => $column['COLUMN_KEY'],
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
}