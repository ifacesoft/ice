<?php

namespace Ice\Data\Source;

use Ice\Core\Data_Provider;
use Ice\Core\Data_Source;
use Ice\Core\Query;
use Ice\Core\Query_Translator;

class Mongodb extends Data_Source
{
    const DATA_PROVIDER_CLASS = 'Ice\Data\Provider\Mongodb';
    const QUERY_TRANSLATOR_CLASS = 'Ice\Query\Translator\Nosql';

    /**
     * Execute query select to data source
     *
     * @param mixed $statement
     * @param Query $query
     * @return array
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    public function executeSelect($statement, Query $query)
    {
        // TODO: Implement executeSelect() method.
    }

    /**
     * Execute query insert to data source
     *
     * @param mixed $statement
     * @param Query $query
     * @return array
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    public function executeInsert($statement, Query $query)
    {
        // TODO: Implement executeInsert() method.
    }

    /**
     * Execute query update to data source
     *
     * @param mixed $statement
     * @param Query $query
     * @return array
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    public function executeUpdate($statement, Query $query)
    {
        // TODO: Implement executeUpdate() method.
    }

    /**
     * Execute query update to data source
     *
     * @param mixed $statement
     * @param Query $query
     * @return array
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    public function executeDelete($statement, Query $query)
    {
        // TODO: Implement executeDelete() method.
    }

    /**
     * Execute query create table to data source
     *
     * @param $statement
     * @param Query $query
     * @return array
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
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
     * @version 0.4
     * @since 0.4
     */
    public function executeDrop($statement, Query $query)
    {
        // TODO: Implement executeDrop() method.
    }

    /**
     * Get data Scheme from data source
     *
     * @return array
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
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
     * @version 0.4
     * @since 0.4
     */
    public function getColumns($tableName)
    {
        // TODO: Implement getColumns() method.
    }

    /**
     * Get table indexes from source
     *
     * @param $tableName
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
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
     * @version 0.4
     * @since 0.4
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
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    public function getDataProviderClass()
    {
        return Mongodb::DATA_PROVIDER_CLASS;
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
        return Mongodb::QUERY_TRANSLATOR_CLASS;
    }
}