<?php
/**
 * Ice core data source container abstract class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

use Ice\Core;
use Ice\Helper\String;

/**
 * Class Data_Source
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
abstract class Data_Source extends Container
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

    /**
     * Return instance of data source
     *
     * @param  Data_Source|string|null $key
     * @param  null $ttl
     * @param array $params
     * @return Data_Source
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
         * @var Data_Source $dataSourceClass
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
         * @var Data_Source $dataSourceClass
         */
        $dataSourceClass = self::getClass();

        $key = 'defaultClassKey_' . $dataSourceClass;
        $repository = Data_Source::getRepository();

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
     * @return Query_Result
     */
    abstract public function executeNativeQuery($query);

    /**
     * Execute query
     *
     * @param Query $query
     * @param $ttl
     * @return Query_Result
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

        /** @var Query_Result $queryResult */
        $queryResult = null;

        $queryType = $query->getQueryBuilder()->getQueryType();

        $queryCommand = 'execute' . ucfirst(strtolower($queryType));

        try {
            if (($queryType == Query_Builder::TYPE_SELECT && $ttl < 0)
                || $queryType == Query_Builder::TYPE_CREATE
                || $queryType == Query_Builder::TYPE_DROP
            ) {
                $queryResult = Query_Result::create($query, $this->$queryCommand($query));

                Profiler::setPoint($queryResult->__toString(), $startTime, $startMemory);
                Logger::log(Profiler::getReport($queryResult->__toString()), 'query (not cache)', 'WARN');

                return $queryResult;
            }

            switch ($queryType) {
                case Query_Builder::TYPE_SELECT:
                    $cacher = Query_Result::getCacher($this->getDataSourceKey());
                    $queryHash = $query->getFullHash();

                    if ($queryResult = $cacher->get($queryHash)) {
                        Profiler::setPoint($queryResult->__toString(), $startTime, $startMemory);
                        Logger::log(Profiler::getReport($queryResult->__toString()/* . "\n". print_r($queryResult->getRows(), true)*/), 'query (cache)', 'LOG');
                        return $queryResult;
                    }

                    $queryResult = Query_Result::create($query, $this->$queryCommand($query));
                    Profiler::setPoint($queryResult->__toString(), $startTime, $startMemory);
                    Logger::log(Profiler::getReport($queryResult->__toString()/* . "\n". print_r($queryResult->getRows(), true)*/), 'query (new)', 'INFO');

                    $cacher->set($queryHash, $queryResult, $ttl);
                    return $queryResult;

                case Query_Builder::TYPE_INSERT:
                case Query_Builder::TYPE_UPDATE:
                case Query_Builder::TYPE_DELETE:
                    $queryResult = Query_Result::create($query, $this->$queryCommand($query))->invalidate();
                    Profiler::setPoint($queryResult->__toString(), $startTime, $startMemory);
                    Logger::log(Profiler::getReport($queryResult->__toString()), 'query (new)', 'INFO');
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
            Logger::log(
                $e->getMessage() . ': ' .
                print_r($query->getBody(), true) . ' (' . print_r($query->getBinds(), true) . ')',
                'query (error)',
                'ERROR'
            );

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
     * @return Query_Translator
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

    protected function init(array $data)
    {
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
}
