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

        /** @var Model $modelClass */
        $modelClass = $query->getModelClass();
        $tableName = $modelClass::getTableName();

        $pkFieldNames = $modelClass::getPkFieldNames();

        $pkFieldName = count($pkFieldNames) == 1 ? reset($pkFieldNames) : null;

        $data[Query_Result::ROWS] = [];

        $filter = isset($statement['where']) && isset($statement['where']['data'])
            ? $statement['where']['data']
            : [];

        foreach ($query->getDataSource()->getConnection()->$tableName->find($filter, $statement['select']['columnNames']) as $row) {
            $pkFieldValue = $row['_id']->{'$id'};
            unset($row['_id']);
            $data[Query_Result::ROWS][$pkFieldValue] = array_merge([$pkFieldName => $pkFieldValue], $row);
        }
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
        $tableName = $modelClass::getTableName();

        try {
            $query->getDataSource()->getConnection()->$tableName->batchInsert($statement['insert']['data']);
        } catch (\Exception $e) {
            foreach ($statement['insert']['data'] as $doc) {
                $query->getDataSource()->getConnection()->$tableName->update(['_id' => $doc['_id']], $doc, ['upsert' => true]);
            }
        }

        $pkFieldNames = $modelClass::getPkFieldNames();

        $pkFieldName = count($pkFieldNames) == 1 ? reset($pkFieldNames) : null;

        $data[Query_Result::ROWS] = [];

        foreach ($statement['insert']['data'] as $row) {
            $pkFieldValue = $row['_id']->{'$id'};
            unset($row['_id']);
            $insertId = [$pkFieldName => $pkFieldValue];
            $data[Query_Result::INSERT_ID][] = $insertId;
            $data[Query_Result::ROWS][$pkFieldValue] = array_merge($insertId, $row);
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
        $tableName = $modelClass::getTableName();

        $this->getConnection()->$tableName->update($statement['where']['data'], $statement['update']['data']);

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
        $data = [];

        $queryTranslatorClass = $this->getQueryTranslatorClass();
        $data[Query_Result::QUERY_BODY] = $queryTranslatorClass::getInstance()->translate($query->getBodyParts());
        $data[Query_Result::QUERY_PARAMS] = $query->getBinds();

        $statement = $this->getStatement($data[Query_Result::QUERY_BODY], $data[Query_Result::QUERY_PARAMS]);

        /** @var Model $modelClass */
        $modelClass = $query->getModelClass();
        $tableName = $modelClass::getTableName();

        $this->getConnection()->$tableName->remove($statement['where']['data']);

        $data[Query_Result::AFFECTED_ROWS] = 1;

        $data[Query_Result::QUERY] = $query;

        return $data;
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
        /** @var Model $modelClass */
        $modelClass = $query->getModelClass();
        $tableName = $modelClass::getTableName();

        $this->getConnection()->$tableName;

        $data[Query_Result::QUERY] = $query;

        return $data;
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
        /** @var Model $modelClass */
        $modelClass = $query->getModelClass();
        $tableName = $modelClass::getTableName();

        $this->getConnection()->$tableName->drop();

        $data[Query_Result::QUERY] = $query;

        return $data;
    }

    /**
     * Get data Scheme from data source
     *
     * @return array
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.4
     */
    public function getTables()
    {
        $tables = [];

        foreach ($this->getConnection()->getCollectionNames() as $name) {
            $tables[$name] = [
                    'engine' => 'MongoDB',
                    'charset' => 'utf-8',
                    'comment' => $name
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
     * @version 0.5
     * @since 0.4
     */
    public function getColumns($tableName)
    {
        return [];
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
                    if (is_array($columnName)) {
                        list($columnName, $operator) = each($columnName);
                    }

                    if (in_array($columnName, $pkColumnNames)) {
                        if (!isset($row['_id'])) {
                            $row['_id'] = new MongoId(array_shift($binds));
                        } else {
                            if (is_array($row['_id'])) {
                                $row['_id']['$in'][] = new MongoId(array_shift($binds));
                            } else {
                                $row['_id'] = ['$in' => [$row['_id'], new MongoId(array_shift($binds))]];
                            }
                        }
                    } else {
                        if (isset($operator)) {
                            if (!isset($row[$operator])) {
                                $row[$operator] = [];
                            }

                            $row[$operator][$columnName] = array_shift($binds);
                        } else {
                            $row[$columnName] = array_shift($binds);
                        }
                    }
                }

                if ($statementType == 'where' || $statementType == 'update') {
                    $data['data'] = $row;
                } else {
                    $data['data'][] = $row;
                }
            }

            unset($data['rowCount']);
            unset($data['columnNames']);
        }

        if (!empty($binds)) {
            Mongodb::getLogger()->exception('Bind params failure', __FILE__, __LINE__, null, $binds);
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