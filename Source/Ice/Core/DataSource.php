<?php
/**
 * Ice core data source container abstract class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

/**
 * Class DataSource
 *
 * Core data source container abstract class
 *
 * @see Ice\Core\Container
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Core
 */
abstract class DataSource extends Container
{
    use Stored;

    /**
     * Data source scheme
     *
     * @var string
     */
    protected $scheme = null;
    /**
     * Data source key
     *
     * @var string
     */
    private $key = null;

    public function __construct(array $data)
    {
        parent::__construct($data);

        list($key, $scheme) = explode('.', $this->getInstanceKey());

        $this->key = $key;
        $this->scheme = $scheme;
        $this->getSourceDataProvider()->setScheme($scheme);
    }

    /**
     * Return source data provider
     *
     * @return DataProvider
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function getSourceDataProvider()
    {
        $sourceDataProviderClass = $this->getDataProviderClass();

        /**
         * @var DataProvider $sourceDataProvider
         */
        $sourceDataProvider = $sourceDataProviderClass::getInstance($this->key);

        if ($sourceDataProvider->getScheme() != $this->scheme) {
            $sourceDataProvider->setScheme($this->scheme);
        }

        return $sourceDataProvider;
    }

    /**
     * Return data provider class
     *
     * @return DataProvider
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    abstract public function getDataProviderClass();

    /**
     * Return instance of data source
     *
     * @param  DataSource|string|null $key
     * @param  null $ttl
     * @param array $params
     * @return DataSource
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since   0.2
     */
    public static function getInstance($key = null, $ttl = null, array $params = [])
    {
        return parent::getInstance($key, $ttl, $params);
    }

    /**
     * Default key of data source
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.0
     */
    protected static function getDefaultKey()
    {
        /**
         * @var DataSource $dataSourceClass
         */
        $dataSourceClass = self::getClass();

        return substr(strstr($dataSourceClass::getDefaultClassKey(), '/'), 1);
    }

    /**
     * Return default class key
     *
     * @return string
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
     */
    public static function getDefaultClassKey()
    {
        /**
         * @var DataSource $dataSourceClass
         */
        $dataSourceClass = self::getClass();

        $key = 'defaultClassKey_' . $dataSourceClass;
        $repository = DataSource::getRepository();

        if ($defaultClassKey = $repository->get($key)) {
            return $defaultClassKey;
        }

        $defaultDataSourceKeys = Module::getInstance()->getDefaultDataSourceKeys();

        return $repository->set($key, reset($defaultDataSourceKeys), 0);
    }

    /**
     * Execute query select to data source
     *
     * @param  Query $query
     * @return array
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    abstract public function executeSelect(Query $query);

    /**
     * Execute query insert to data source
     *
     * @param  Query $query
     * @return array
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    abstract public function executeInsert(Query $query);

    /**
     * Execute query update to data source
     *
     * @param  Query $query
     * @return array
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    abstract public function executeUpdate(Query $query);

    /**
     * Execute query update to data source
     *
     * @param  Query $query
     * @return array
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    abstract public function executeDelete(Query $query);

    /**
     * Execute query create table to data source
     *
     * @param  Query $query
     * @return array
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    abstract public function executeCreate(Query $query);

    /**
     * Execute query drop table to data source
     *
     * @param  Query $query
     * @return array
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    abstract public function executeDrop(Query $query);

    /**
     * Get data Scheme from data source
     *
     * @param  Module $module
     * @return array
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    abstract public function getTables(Module $module);

    /**
     * Get table scheme from source
     *
     * @param  $tableName
     * @return array
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    abstract public function getColumns($tableName);

    /**
     * Get table indexes from source
     *
     * @param  $tableName
     * @return array
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    abstract public function getIndexes($tableName);

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
    abstract public function getReferences($tableName);

    /**
     * Execute native query
     *
     * @param $query
     * @return QueryResult
     */
    abstract public function executeNativeQuery($query);

    /**
     * Execute query
     *
     * @param Query $query
     * @param $ttl
     * @return QueryResult
     * @throws \Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since 0.2
     */
    public function executeQuery(Query $query, $ttl)
    {
        $startTime = Profiler::getMicrotime();
        $startMemory = Profiler::getMemoryGetUsage();

        /** @var QueryResult $queryResult */
        $queryResult = null;

        $queryType = $query->getQueryBuilder()->getQueryType();

        $queryCommand = 'execute' . ucfirst(strtolower($queryType));

        try {
            if (($queryType == QueryBuilder::TYPE_SELECT && $ttl < 0)
                || $queryType == QueryBuilder::TYPE_CREATE
                || $queryType == QueryBuilder::TYPE_DROP
            ) {
                $queryResult = QueryResult::create($query, $this->$queryCommand($query));

                Profiler::setPoint($queryResult->__toString(), $startTime, $startMemory);
                Logger::log(Profiler::getReport($queryResult->__toString()), 'data source (not cache)', 'WARN');

                return $queryResult;
            }

            switch ($queryType) {
                case QueryBuilder::TYPE_SELECT:
                    $cacher = QueryResult::getCacher($this->getDataSourceKey());

                    /** @var DataProvider $dataProviderClass */
                    $dataProviderClass = get_class($cacher);
                    $dataProviderClassName = $dataProviderClass::getClassName();

                    $queryHash = $query->getFullHash();

                    if ($queryResult = $cacher->get($queryHash)) {
                        Profiler::setPoint($queryResult->__toString(), $startTime, $startMemory);
                        Logger::log(Profiler::getReport($queryResult->__toString()), 'data source (cache - ' . $dataProviderClassName . ')', 'LOG');
                        return $queryResult;
                    }

                    $queryResult = QueryResult::create($query, $this->$queryCommand($query));
                    Profiler::setPoint($queryResult->__toString(), $startTime, $startMemory);
                    Logger::log(Profiler::getReport($queryResult->__toString()), 'data source (new - ' . $dataProviderClassName . ')', 'INFO');

                    $cacher->set([$queryHash => $queryResult], $ttl);
                    return $queryResult;

                case QueryBuilder::TYPE_INSERT:
                case QueryBuilder::TYPE_UPDATE:
                case QueryBuilder::TYPE_DELETE:
                    $queryResult = QueryResult::create($query, $this->$queryCommand($query))->invalidate();
                    Profiler::setPoint($queryResult->__toString(), $startTime, $startMemory);
                    Logger::log(Profiler::getReport($queryResult->__toString()), 'data source (query)', 'INFO');
                    return $queryResult;

                default:
                    Logger::getInstance(__CLASS__)->exception(
                        ['Unknown data source query statement type {$0}', $queryType],
                        __FILE__,
                        __LINE__,
                        null,
                        $query
                    );
            }
        } catch (\Exception $e) {
            $message = $e->getMessage() . ': ' . preg_replace('/\s\s+/', ' ', print_r($query->getBody(), true) . ' (' . print_r($query->getBinds(), true) . ')');

            Profiler::setPoint($message, $startTime, $startMemory);
            Logger::log(Profiler::getReport($message), 'data source (error)', 'ERROR');

            throw $e;
        }

        return null;
    }

    /**
     * Return data source key
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since   0.5
     */
    public function getDataSourceKey()
    {
        return get_class($this) . '/' . $this->getKey() . '.' . $this->getScheme();
    }

    /**
     * Return current key
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since   0.5
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Return current scheme
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since   0.5
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * Prepare query statement for query
     *
     * @param  $body
     * @param  array $binds
     * @return mixed
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    abstract public function getStatement($body, array $binds);

    /**
     * Return query translator class
     *
     * @return QueryTranslator
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    abstract public function getQueryTranslatorClass();

    /**
     * Begin transaction
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     * @param string $isolationLevel
     * @return
     */
    abstract public function beginTransaction($isolationLevel = null);

    /**
     * Commit transaction
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    abstract public function commitTransaction();

    /**
     * Rollback transaction
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    abstract public function rollbackTransaction();

    /**
     * Create save point
     *
     * @param $savePoint
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    abstract public function savePoint($savePoint);

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
    abstract public function rollbackSavePoint($savePoint);

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
    abstract public function releaseSavePoint($savePoint);

    /**
     * Get by ice query language
     *
     * ```php
     * // select
     *  $iceql = [
     *      'test.ice_session:s/session_data,ip|ASC|GROUP:client_ip,agent' => 'session_pk|OR:sdas|<>,views:0',
     *      'test.ice_user:u.user_pk=s.user__fk/user_name' => 'user_active:1|<>'
     *  ];
     * ```
     *
     * @param  string|array $iceql
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   1.1
     */
    public function get($iceql)
    {
        $iceql = (array)$iceql;

        $result = $this->query($this->translateGet($iceql));

        if ($this->getConnection()->errno) {
            Logger::getInstance(__CLASS__)->error(
                ['mysql - #' . $this->getConnection()->errno . ': {$0}', $this->getConnection()->error],
                __FILE__,
                __LINE__
            );
            return [];
        }

        $data = [];

        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        $result->close();

        return $data;
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
    abstract public function query($sql);

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
    abstract public function translateGet(array $iceql);

    /**
     * Get connection instance
     *
     * @return Object
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function getConnection()
    {
        return $this->getSourceDataProvider()->getConnection();
    }

    /**
     * Set by ice query language
     *
     *```php
     * // insert
     *  $iceql = [
     *      'test.ice_session:s/session_data:testtdate,ip:127.0.0.1,agent:firefox'
     *  ];
     *
     * // update
     *  $iceql = [
     *      'test.ice_session:s/session_data:testtdate,ip:127.0.0.1,agent:firefox' => 'session_pk:4324'
     *  ];
     *```
     *
     * @param array $iceql
     * @return mixed setted value
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    public function set(array $iceql)
    {
        $iceql = (array)$iceql;

        $result = $this->query($this->translateSet($iceql));

        if ($this->getConnection()->errno) {
            Logger::getInstance(__CLASS__)->error(
                ['mysql - #' . $this->getConnection()->errno . ': {$0}', $this->getConnection()->error],
                __FILE__,
                __LINE__
            );
            return null;
        }

        return $result;
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
    abstract public function translateSet(array $iceql);

    /**
     * Delete by ice query language
     *
     * ```php
     * // delete
     *  $iceql = [
     *      'test.ice_session:s => 'session_pk|OR:sdas|<>,views:0',
     *  ];
     * ```
     *
     * @param array $iceql
     * @return bool|mixed
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    public function delete(array $iceql)
    {
        $iceql = (array)$iceql;

        $result = $this->query($this->translateSet($iceql));

        if ($this->getConnection()->errno) {
            Logger::getInstance(__CLASS__)->error(
                ['mysql - #' . $this->getConnection()->errno . ': {$0}', $this->getConnection()->error],
                __FILE__,
                __LINE__
            );
            return null;
        }

        return $result;
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
    abstract public function translateDelete(array $iceql);
}
