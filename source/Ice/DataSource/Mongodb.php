<?php

namespace Ice\DataSource;

use Ice\Core\DataProvider;
use Ice\Core\DataSource;
use Ice\Core\Logger;
use Ice\Core\Model;
use Ice\Core\Module;
use Ice\Core\Query;
use Ice\Core\QueryResult;
use Ice\Core\QueryTranslator;
use Ice\Helper\Json;
use Ice\Helper\Type_String;
use MongoCursor;
use MongoId;

class Mongodb extends DataSource
{
    const DATA_PROVIDER_CLASS = 'Ice\DataProvider\Mongodb';
    const QUERY_TRANSLATOR_CLASS = 'Ice\QueryTranslator\Mongodb';

    /**
     * Return instance of mongodb
     *
     * @param  null $instanceKey
     * @param  null $ttl
     * @param array $params
     * @return Mongodb
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
     * @throws \Ice\Core\Exception
     */
    public static function getInstance($instanceKey = null, $ttl = null, array $params = [])
    {
        return parent::getInstance($instanceKey, $ttl, $params);
    }

    /**
     * Execute query select to data source
     *
     * @param  Query $query
     * @param bool $indexKeys
     * @return array
     * @throws \Ice\Core\Exception
     * @throws \Ice\Exception\Error
     * @throws \Ice\Exception\FileNotFound
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
     */
    public function executeSelect(Query $query, $indexKeys = true)
    {
        $data = [];

        $statement = $this->getStatement($query->getBody(), $query->getBinds());

        /**
         * @var Model $modelClass
         */
        $modelClass = $query->getQueryBuilder()->getModelClass();
        $tableName = $modelClass::getTableName();

        $pkFieldNames = $modelClass::getScheme()->getPkFieldNames();

        $pkFieldName = count($pkFieldNames) == 1 ? reset($pkFieldNames) : null;

        $data[QueryResult::ROWS] = [];

        $filter = isset($statement['where']) && isset($statement['where']['data'])
            ? $statement['where']['data']
            : [];

        try {
            /**
             * @var MongoCursor $cursor
             */
            $cursor = $this->getConnection()->$tableName->find($filter, $statement['select']['columnNames']);

            if (isset($statement['order'])) {
                $cursor = $cursor->sort($statement['order']['columnNames']);
            }

            if (isset($statement['limit'])) {
                if (!empty($statement['limit']['skip'])) {
                    $cursor = $cursor->skip($statement['limit']['skip']);
                }
                if (!empty($statement['limit']['limit'])) {
                    $cursor = $cursor->limit($statement['limit']['limit']);
                }
            }

            foreach ($cursor as $row) {
                $pkFieldValue = $row['_id']->{'$id'};
                unset($row['_id']);
                $data[QueryResult::ROWS][$pkFieldValue] = array_merge([$pkFieldName => $pkFieldValue], $row);
            }
        } catch (\MongoException $e) {
            Logger::getInstance(__CLASS__)->exception(
                [
                    '#' . $e->getCode() . ': {$0} - {$1} [{$2}]',
                    [$e->getMessage(), print_r($query->getBody(), true), implode(', ', $query->getBinds())]
                ],
                __FILE__,
                __LINE__,
                $e
            );
        }

        $data[QueryResult::NUM_ROWS] = count($data[QueryResult::ROWS]);

        return $data;
    }

    /**
     * Prepare query statement for query
     *
     * @param  $body
     * @param  array $binds
     * @return mixed
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
     */
    public function getStatement($body, array $binds)
    {
        foreach ($body as $statementType => &$data) {
            if ($statementType == 'select' || $statementType == 'order' || $statementType == 'limit') {
                continue;
            }

            /**
             * @var Model $modelClass
             */
            $modelClass = $data['modelClass'];

            $pkColumnNames = $modelClass::getScheme()->getPkColumnNames();

            $data['data'] = [];

            if (!isset($data['rowCount'])) {
                $data['rowCount'] = 1;
            }

            for ($i = 0; $i < $data['rowCount']; $i++) {
                $row = [];

                foreach ($data['columnNames'] as $columnName => $array) {
                    foreach ((array)$array as $operator) {
                        if (in_array($columnName, $pkColumnNames)) {
                            $id = null;
                            try {
                                $id = array_shift($binds);
                                if (!isset($row['_id'])) {
                                    $row['_id'] = new MongoId($id);
                                } else {
                                    if (is_array($row['_id'])) {
                                        $row['_id']['$in'][] = new MongoId($id);
                                    } else {
                                        $row['_id'] = ['$in' => [$row['_id'], new MongoId($id)]];
                                    }
                                }
                            } catch (\MongoException $e) {
                                Logger::getInstance(__CLASS__)->exception('Build statement failed', __FILE__, __LINE__, $e, $id);
                            }
                        } else {
                            $row[$columnName] = isset($operator)
                                ? [$operator => array_shift($binds)]
                                : array_shift($binds);
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
            Logger::getInstance(__CLASS__)->exception(
                'Bind params failure',
                __FILE__,
                __LINE__,
                null,
                ['body' => $body, 'binds' => $binds]
            );
        }

        return $body;
    }

    /**
     * Get connection instance
     *
     * @return \MongoDb
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
     */
    public function getConnection()
    {
        return $this->getSourceDataProvider()->getConnection()->selectDB($this->scheme);
    }

    /**
     * Execute query insert to data source
     *
     * @param  Query $query
     * @return array
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
     */
    public function executeInsert(Query $query)
    {
        $data = [];

        $statement = $this->getStatement($query->getBody(), $query->getBinds());

        /**
         * @var Model $modelClass
         */
        $modelClass = $query->getQueryBuilder()->getModelClass();
        $tableName = $modelClass::getTableName();

        try {
            $this->getConnection()->$tableName->batchInsert($statement['insert']['data']);
        } catch (\Exception $e) {
            foreach ($statement['insert']['data'] as $doc) {
                $this->getConnection()->$tableName->update(['_id' => $doc['_id']], $doc, ['upsert' => true]);
            }
        }

        $pkFieldNames = $modelClass::getScheme()->getPkFieldNames();

        $pkFieldName = count($pkFieldNames) == 1 ? reset($pkFieldNames) : null;

        $data[QueryResult::ROWS] = [];

        foreach ($statement['insert']['data'] as $row) {
            $pkFieldValue = $row['_id']->{'$id'};
            unset($row['_id']);
            $insertId = [$pkFieldName => $pkFieldValue];
            $data[QueryResult::INSERT_ID][] = $insertId;
            $data[QueryResult::ROWS][$pkFieldValue] = array_merge($insertId, $row);
        }

        $data[QueryResult::AFFECTED_ROWS] = count($statement['insert']['data']);

        return $data;
    }

    /**
     * Execute query update to data source
     *
     * @param  Query $query
     * @return array
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
     */
    public function executeUpdate(Query $query)
    {
        $data = [];

        $statement = $this->getStatement($query->getBody(), $query->getBinds());

        /**
         * @var Model $modelClass
         */
        $modelClass = $query->getQueryBuilder()->getModelClass();
        $tableName = $modelClass::getTableName();

        $this->getConnection()->$tableName->update($statement['where']['data'], $statement['update']['data']);

        $data[QueryResult::AFFECTED_ROWS] = 1;

        return $data;
    }

    /**
     * Execute query update to data source
     *
     * @param  Query $query
     * @return array
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
     */
    public function executeDelete(Query $query)
    {
        $data = [];

        $statement = $this->getStatement($query->getBody(), $query->getBinds());

        /**
         * @var Model $modelClass
         */
        $modelClass = $query->getQueryBuilder()->getModelClass();
        $tableName = $modelClass::getTableName();

        $this->getConnection()->$tableName->remove($statement['where']['data']);

        $data[QueryResult::AFFECTED_ROWS] = 1;

        return $data;
    }

    /**
     * Execute query create table to data source
     *
     * @param  Query $query
     * @return array
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
     */
    public function executeCreate(Query $query)
    {
        /**
         * @var Model $modelClass
         */
        $modelClass = $query->getQueryBuilder()->getModelClass();
        $tableName = $modelClass::getTableName();

        $this->getConnection()->$tableName;

        return [];
    }

    /**
     * Execute query drop table to data source
     *
     * @param  Query $query
     * @return array
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
     */
    public function executeDrop(Query $query)
    {
        /**
         * @var Model $modelClass
         */
        $modelClass = $query->getQueryBuilder()->getModelClass();
        $tableName = $modelClass::getTableName();

        $this->getConnection()->$tableName->drop();

        return [];
    }

    /**
     * Get data Scheme from data source
     *
     * @param  Module $module
     * @return array
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.4
     */
    public function getTables(Module $module)
    {
        $tables = [];

        foreach ($this->getConnection()->getCollectionNames() as $name) {
            $tables[$name] = [];

            $data = &$tables[$name];

            $data = [
                'revision' => date('mdHi') . '_' . strtolower(Type_String::getRandomString(2)),
                'dataSourceKey' => $this->getDataSourceKey(),
                'scheme' => [],
                'schemeHash' => crc32(Json::encode([])),
                'columns' => [],
            ];

            $dataScheme = &$data['scheme'];
            $dataScheme = [
                'tableName' => $name,
                'engine' => 'MongoDB',
                'charset' => 'utf-8',
                'comment' => $name
            ];
            $data['schemeHash'] = crc32(Json::encode($dataScheme));

            $data['indexes'] = [];
            $data['indexesHash'] = crc32(Json::encode($data['indexes']));

            $data['columns'] = [];
            $data['columnsHash'] = crc32(Json::encode($data['columns']));
        }

        return $tables;
    }

    /**
     * Get table scheme from source
     *
     * @param  $tableName
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since   0.4
     */
    public function getColumns($tableName)
    {
        return [];
    }

    /**
     * Get table indexes from source
     *
     * @param  $tableName
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
     */
    public function getIndexes($tableName)
    {
        return [
            'PRIMARY KEY' => [
                'PRIMARY' => [
                    1 => 'id',
                ]
            ],
            'FOREIGN KEY' => [],
            'UNIQUE' => []
        ];
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
        return Mongodb::DATA_PROVIDER_CLASS;
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
        return Mongodb::QUERY_TRANSLATOR_CLASS;
    }

    /**
     * Begin transaction
     *
     * @param string $isolationLevel
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    public function beginTransaction($isolationLevel = null)
    {
        // TODO: Implement beginTransaction() method.
    }

    /**
     * Commit transaction
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
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
     * @since   0
     * @param null $e
     */
    public function rollbackTransaction($e = null)
    {
        // TODO: Implement rollbackTransaction() method.
    }


    /**
     * Get table references from source
     *
     * @param  $tableName
     * @return array
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    public function getReferences($tableName)
    {
        return [];
    }

    /**
     * Create save point
     *
     * @param $savePoint
     */
    public function savePoint($savePoint)
    {
        // TODO: Implement savePointTransaction() method.
    }

    /**
     * Rollback save point
     *
     * @param $savePoint
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    public function rollbackSavePoint($savePoint)
    {
        // TODO: Implement rollbackSavePoint() method.
    }

    /**
     * Commit save point
     *
     * @param $savePoint
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    public function releaseSavePoint($savePoint)
    {
        // TODO: Implement releaseSavePoint() method.
    }

    /**
     * Execute native query
     *
     * @param $query
     * @return QueryResult
     */
    public function executeNativeQuery($query)
    {
        // TODO: Implement executeNativeQuery() method.
    }

    /**
     * Execute native query
     *
     * @param $sql
     * @return mixed
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    public function query($sql)
    {
        // TODO: Implement query() method.
    }

    /**
     * Translate ice query language for get data
     *
     * @param $iceql
     * @return mixed
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    public function translateGet(array $iceql)
    {
        // TODO: Implement translateGet() method.
    }

    /**
     * Translate ice query language for set data
     *
     * @param $iceql
     * @return mixed setted value
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    public function translateSet(array $iceql)
    {
        // TODO: Implement translateSet() method.
    }

    /**
     * Translate ice query language for delete data
     *
     * @param $iceql
     * @return bool|mixed
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    public function translateDelete(array $iceql)
    {
        // TODO: Implement translateDelete() method.
    }

    public function escapeString($string)
    {
        // TODO: Implement escapeString() method.
    }
}
