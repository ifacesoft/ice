<?php

namespace Ice\Data\Source;

use Ice\Core\Data_Provider;
use Ice\Core\Data_Source;
use Ice\Core\Exception;
use Ice\Core\Logger;
use Ice\Core\Model;
use Ice\Core\Query;
use Ice\Core\Query_Result;
use Ice\Core\Query_Translator;
use MongoId;

class Mongodb extends Data_Source
{
    const DATA_PROVIDER_CLASS = 'Ice\Data\Provider\Mongodb';
    const QUERY_TRANSLATOR_CLASS = 'Ice\Query\Translator\Mongodb';

    /**
     * Execute query select to data source
     *
     * @param Query $query
     * @return array
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    public function executeSelect(Query $query)
    {
        $data = [];

        $queryTranslatorClass = $this->getQueryTranslatorClass();
        $data[Query_Result::QUERY_BODY] = $queryTranslatorClass::getInstance()->translate($query->getBodyParts());
        $data[Query_Result::QUERY_PARAMS] = $query->getBinds();

        $statement = $this->getStatement($data[Query_Result::QUERY_BODY], $data[Query_Result::QUERY_PARAMS]);

        Logger::debug($statement);

        /** @var Model $modelClass */
        $modelClass = $query->getModelClass();
        $modelName = $modelClass::getClassName();

        $data[Query_Result::ROWS] = [];

        foreach ($query->getDataSource()->getConnection()->$modelName->find($statement['where']['data'], $statement['select']['columnNames']) as $row) {
            $data[Query_Result::ROWS][$row['_id']->{'$id'}] = $row;
        }

        Logger::debug($data[Query_Result::ROWS]);die();

        $data[Query_Result::NUM_ROWS] = count($data[Query_Result::ROWS]);

        $data[Query_Result::QUERY] = $query;

        return $data;
    }

    /**
     * Execute query insert to data source
     *
     * @param Query $query
     * @return array
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    public function executeInsert(Query $query)
    {
        $data = [];

        $queryTranslatorClass = $this->getQueryTranslatorClass();
        $data[Query_Result::QUERY_BODY] = $queryTranslatorClass::getInstance()->translate($query->getBodyParts());
        $data[Query_Result::QUERY_PARAMS] = $query->getBinds();

        $statement = $this->getStatement($data[Query_Result::QUERY_BODY], $data[Query_Result::QUERY_PARAMS]);

        /** @var Model $modelClass */
        $modelClass = $query->getModelClass();
        $modelName = $modelClass::getClassName();

        $query->getDataSource()->getConnection()->$modelName->batchInsert($statement['insert']['data']);

        $pkFieldNames = $modelClass::getPkFieldNames();

        $pkFieldName = count($pkFieldNames) == 1 ? reset($pkFieldNames) : null;

        $data[Query_Result::ROWS] = [];

        foreach ($statement['insert']['data'] as $row) {
            $id = $row['_id']->{'$id'};
            $insertId = [$pkFieldName => $id];
            $data[Query_Result::INSERT_ID][] = $insertId;
            $data[Query_Result::ROWS][$id] = array_merge($insertId, $row);
        }

        $data[Query_Result::AFFECTED_ROWS] = count($statement['insert']['data']);
        $data[Query_Result::QUERY] = $query;

        return $data;
    }

    /**
     * Execute query update to data source
     *
     * @param Query $query
     * @return array
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    public function executeUpdate(Query $query)
    {
        $data = [];

        $queryTranslatorClass = $this->getQueryTranslatorClass();
        $data[Query_Result::QUERY_BODY] = $queryTranslatorClass::getInstance()->translate($query->getBodyParts());
        $data[Query_Result::QUERY_PARAMS] = $query->getBinds();

        $statement = $this->getStatement($data[Query_Result::QUERY_BODY], $data[Query_Result::QUERY_PARAMS]);

        /** @var Model $modelClass */
        $modelClass = $query->getModelClass();
        $modelName = $modelClass::getClassName();

        $query->getDataSource()->getConnection()->$modelName->update($statement['where']['data'], [$statement['update']['data']]);

        $data[Query_Result::AFFECTED_ROWS] = 1;

        $data[Query_Result::QUERY] = $query;

        return $data;
    }

    /**
     * Execute query update to data source
     *
     * @param Query $query
     * @return array
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    public function executeDelete(Query $query)
    {
        throw new Exception('Not implemented');
    }

    /**
     * Execute query create table to data source
     *
     * @param Query $query
     * @return array
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    public function executeCreate(Query $query)
    {
        throw new Exception('Not implemented');
    }

    /**
     * Execute query drop table to data source
     *
     * @param Query $query
     * @return array
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    public function executeDrop(Query $query)
    {
        throw new Exception('Not implemented');
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
     * @param $body
     * @param array $binds
     * @return mixed
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    public function getStatement($body, array $binds)
    {
        foreach ($body as $statementType => &$data) {
            if ($statementType == 'select') {
                continue;
            }

            /** @var Model $modelClass */
            $modelClass = $data['modelClass'];

            $pkColumnNames = $modelClass::getScheme()->getPkColumnNames();

            $data['data'] = [];

            if (!isset($data['rowCount'])) {
                $data['rowCount'] = 1;
            }

            for ($i = 0; $i < $data['rowCount']; $i++) {
                $row = [];

                foreach ($data['columnNames'] as $columnName) {
                    if (in_array($columnName, $pkColumnNames)) {
                        $row['_id'] = new MongoId(array_shift($binds));
                    } else {
                        $row[$columnName] = array_shift($binds);
                    }
                }

                if ($statementType == 'where') {
                    $data['data'] = $row;
                } else {
                    $data['data'][] = $row;
                }
            }

            unset($data['rowCount']);
            unset($data['columnNames']);
        }

        return $body;
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

    /**
     * Get connection instance
     *
     * @return \MongoDb
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    public function getConnection()
    {
        return $this->getSourceDataProvider()->getConnection()->selectDB($this->_scheme);
    }

    /**
     * Return instance of mongodb
     *
     * @param null $key
     * @param null $ttl
     * @return Mongodb
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    public static function getInstance($key = null, $ttl = null)
    {
        return parent::getInstance($key, $ttl);
    }
}