<?php
/**
 * Ice data source implementation defined class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Data\Source;

use Ice\Core\Data_Provider;
use Ice\Core\Data_Source;
use Ice\Core\Model;
use Ice\Core\Query;
use Ice\Core\Query_Builder;
use Ice\Core\Query_Result;
use Ice\Core\Query_Translator;
use Ice\Helper\Query as Helper_Query;

/**
 * Class Defined
 *
 * Implements defined data source
 *
 * @see Ice\Core\Data_Source
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Data_Source
 *
 * @version 0.0
 * @since 0.0
 */
class Defined extends Data_Source
{
    /**
     * Execute query select to data source
     *
     * @param mixed $statement
     * @param Query $query
     * @return array
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function executeSelect($statement, Query $query)
    {
        /** @var Model $modelClass */
        $modelClass = $query->getModelClass();
        $rows = $this->getConnection($modelClass);

        $pkName = $modelClass::getFieldName('/pk');

        $fieldColumnMap = $modelClass::getScheme()->getFieldColumnMap();
        $flippedFieldNames = array_flip($fieldColumnMap);

        $definedRows = [];
        foreach ($rows as $pk => &$row) {
            $definedRow = [];
            foreach ($row as $fieldName => $fieldValue) {
                if (isset($flippedFieldNames[$fieldName])) { // Пока такой костыль.. надо думать //dp
                    $definedRow[$flippedFieldNames[$fieldName]] = $fieldValue;
                } else {
                    $definedRow[$fieldName] = $fieldValue;
                }
            }
            $definedRow[$fieldColumnMap[$pkName]] = $pk;
            $definedRows[] = $definedRow;
        }
        $rows = &$definedRows;

        $filterFunction = function ($where) {
            return function ($row) use ($where) {
                foreach ($where as $part) {
                    $whereQuery = null;

                    switch ($part[2]) {
                        case Query_Builder::SQL_COMPARISON_OPERATOR_EQUAL:
                            if (!isset($row[$part[1]]) || $row[$part[1]] != reset($part[3])) {
                                return false;
                            }
                            break;
                        case Query_Builder::SQL_COMPARISON_OPERATOR_NOT_EQUAL:
                            if ($row[$part[1]] == reset($part[3])) {
                                return false;
                            }
                            break;
                        case Query_Builder::SQL_COMPARISON_KEYWORD_IN:
                            if (!in_array($row[$part[1]], $part[3])) {
                                return false;
                            }
                            break;
                        case Query_Builder::SQL_COMPARISON_KEYWORD_IS_NULL:
                            if ($row[$part[1]] !== null) {
                                return false;
                            }
                            break;
                        case Query_Builder::SQL_COMPARISON_KEYWORD_IS_NOT_NULL:
                            if ($row[$part[1]] === null) {
                                return false;
                            }
                            break;
                        default:
                            Defined::getLogger()->exception(['Unknown comparsion operator {$0}', $part[2]], __FILE__, __LINE__);
                    }
                }

                return true;
            };
        };

        $rows = array_filter($rows, $filterFunction(Helper_Query::convertWhereForFilter($query)));

        return [
            Query_Result::ROWS => $rows,
            Query_Result::NUM_ROWS => count($rows)
        ];
    }

    /**
     * Execute query insert to data source
     *
     * @param Query $query
     * @return array
     * @throws \Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function executeInsert(Query $query)
    {
        throw new \Exception('Implement insert() method.');
    }

    /**
     * Execute query update to data source
     *
     * @param Query $query
     * @return array
     * @throws \Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function executeUpdate(Query $query)
    {
        throw new \Exception('Implement update() method.');
    }

    /**
     * Execute query update to data source
     *
     * @param Query $query
     * @return array
     * @throws \Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function executeDelete(Query $query)
    {
        throw new \Exception('Implement delete() method.');
    }

    /**
     * Return data scheme
     *
     * @return mixed
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getDataScheme()
    {
        return Data_Source::getDefault()->getDataScheme();
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
        // TODO: Implement getTables() method.
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
        // TODO: Implement getColumns() method.
    }

    /**
     * Execute query create table to data source
     *
     * @param $statement
     * @param Query $query
     * @return array
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function executeCreate($statement, Query $query)
    {
        // TODO: Implement executeCreate() method.
    }

    /**
     * Execute query drop table to data source
     *
     * @param mixed $statement
     * @param Query $query
     * @return array
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function executeDrop($statement, Query $query)
    {
        // TODO: Implement executeDrop() method.
    }

    /**
     * Get table indexes from source
     *
     * @param $tableName
     * @return array
     *
     * @author anonymous <email>
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.3
     * @since 0.3
     */
    public function getIndexes($tableName)
    {
        // TODO: Implement getIndexes() method.
    }

    /**
     * Prepare query statement for query
     *
     * @param Query $query
     * @return mixed
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.2
     */
    public function getStatement(Query $query)
    {
        // TODO: Implement getStatement() method.
    }

    /**
     * Return data provider class
     *
     * @return Data_Provider
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since 0
     */
    public function getDataProviderClass()
    {
        // TODO: Implement getDataProviderClass() method.
    }

    /**
     * Return query translator class
     *
     * @return Query_Translator
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since 0
     */
    public function getQueryTranslatorClass()
    {
        // TODO: Implement getQueryTranslatorClass() method.
    }

    /**
     * Begin transaction
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since 0
     */
    public function beginTransaction()
    {
        // TODO: Implement beginTransaction() method.
    }

    /**
     * Commit transaction
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since 0
     */
    public function commitTransaction()
    {
        // TODO: Implement commitTransaction() method.
    }

    /**
     * Rollback transaction
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since 0
     */
    public function rollbackTransaction()
    {
        // TODO: Implement rollbackTransaction() method.
    }
}