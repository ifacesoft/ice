<?php
/**
 * Ice core data source container abstract class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

use Ice;
use Ice\Core;
use Ice\Data\Provider\Mysqli as Data_Provider_Mysqli;
use Ice\Helper\Arrays;
use Ice\Helper\Memory;
use Ice\Helper\Object;

/**
 * Class Data_Source
 *
 * Core data source container abstract class
 *
 * @see Ice\Core\Container
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Core
 *
 * @version 0.0
 * @since 0.0
 */
abstract class Data_Source extends Container
{
    use Core;

    /**
     * Data provider key for this data source
     *
     * @var string
     */
    private $_sourceDataProviderKey = null;

    /**
     * Connected data scheme
     *
     * @var string
     */
    private $_scheme = null;

    /**
     * Private constructor for dat source
     *
     * @param $scheme
     * @param $sourceDataProviderKey
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    private function __construct($scheme, $sourceDataProviderKey)
    {
        $this->_scheme = $scheme;
        $this->_sourceDataProviderKey = $sourceDataProviderKey;
        $this->getSourceDataProvider()->setScheme($scheme);
    }

    /**
     * Return source data provider
     *
     * @return Data_Provider
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getSourceDataProvider()
    {
        /** @var Data_Provider_Mysqli $sourceDataProvider */
        $sourceDataProvider = Data_Provider::getInstance($this->_sourceDataProviderKey);

        if ($sourceDataProvider->getScheme() != $this->_scheme) {
            $sourceDataProvider->setScheme($this->_scheme);
        }

        return $sourceDataProvider;
    }

    /**
     * Default key of data source
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public static function getDefaultKey()
    {
        $schemes = Data_Source::getConfig()->get();
        return is_array($schemes) ? reset($schemes) : $schemes;
    }

    /**
     * Create new instance of data source
     *
     * @param $scheme
     * @param $hash
     * @return Data_Source
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    protected static function create($scheme, $hash = null)
    {
        $sourceDataProviderKey = null;

        foreach (Data_Source::getConfig()->gets() as $dataSourceKey => $configSchemes) {
            foreach ((array)$configSchemes as $configScheme) {
                if ($configScheme == $scheme) {
                    $sourceDataProviderKey = $dataSourceKey;
                    break 2;
                }
            }
        }

        if (!$sourceDataProviderKey) {
            Data_Source::getLogger()->fatal(['Data source not found for scheme {$0}', $scheme], __FILE__, __LINE__);
        }

        $dataSourceClass = Object::getClass(Data_Source::getClass(), strstr($sourceDataProviderKey, '/', true));
        return new $dataSourceClass($scheme, $sourceDataProviderKey);
    }

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
    abstract public function executeSelect($statement, Query $query);

    /**
     * Execute query insert to data source
     *
     * @param mixed $statement
     * @param Query $query
     * @return array
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    abstract public function executeInsert($statement, Query $query);

    /**
     * Execute query update to data source
     *
     * @param mixed $statement
     * @param Query $query
     * @return array
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    abstract public function executeUpdate($statement, Query $query);

    /**
     * Execute query update to data source
     *
     * @param mixed $statement
     * @param Query $query
     * @return array
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    abstract public function executeDelete($statement, Query $query);

    /**
     * Execute query create table to data source
     *
     * @param Query $query
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    abstract public function executeCreate($statement, Query $query);

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
    abstract public function executeDrop($statement, Query $query);

    /**
     * Get connection instance
     *
     * @return Object
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getConnection()
    {
        return $this->getSourceDataProvider()->getConnection();
    }

    /**
     * Return cache data provider
     *
     * @return Data_Provider
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getCacheDataProvider()
    {
        return Data_Source::getInstance('cache');
    }

    /**
     * Return instance of data source
     *
     * @param null $key
     * @param null $ttl
     * @return Data_Source
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.2
     */
    public static function getInstance($key = null, $ttl = null)
    {
        return parent::getInstance($key, $ttl);
    }

    /**
     * Get data Scheme from data source
     *
     * @return array
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since 0
     */
    public abstract function getTables();

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
    public abstract function getColumns($tableName);

    /**
     * Get table indexes from source
     *
     * @param $tableName
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.3
     * @since 0.3
     */
    public abstract function getIndexes($tableName);

    /**
     * @param Query $query
     * @param $ttl
     * @return Query_Result
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.2
     */
    public function execute(Query $query, $ttl)
    {
        $startTime = Logger::microtime();

        $queryResult = null;

        $cache = ['tags' => $query->getValidateTags(), 'time' => 0, 'data' => []];

        try {
            $queryType = $query->getQueryType();
            if (
                ($queryType == Query_Builder::TYPE_SELECT && !$ttl) ||
                $queryType == Query_Builder::TYPE_CREATE ||
                $queryType == Query_Builder::TYPE_DROP
            ) {
                return $this->getQueryResult($query);
            }

            switch ($queryType) {
                case Query_Builder::TYPE_SELECT:
                    $hash = $query->getFullHash();

                    $cacheDataProvider = Query::getDataProvider('query');

                    $cache = Arrays::defaults($cache, $cacheDataProvider->get($hash));

                    if (Cache::validate(__CLASS__, $cache['tags'], $cache['time'])) {
                        if (!Environment::isProduction()) {
                            $message = 'sql cache: ' . str_replace("\t", '', str_replace("\n", ' ', $query->getSql())) . ' [' . implode(', ', $query->getBinds()) . '] ' . Logger::microtimeResult($startTime);

                            if (Request::isCli()) {
                                Query::getLogger()->info($message . ' ' . Memory::memoryGetUsagePeak(), Logger::SUCCESS, false);
                            } else {
                                Logger::fb($message);
                            }
                        }

                        return Query_Result::create($query->getModelClass(), $cache['data']);
                    }

                    $queryResult = $this->getQueryResult($query);

                    $cache['data'] = $queryResult->getResult();
                    $cache['time'] = time();

                    $cacheDataProvider->set($hash, $cache, $ttl);
                    break;

                case Query_Builder::TYPE_INSERT:
                case Query_Builder::TYPE_UPDATE:
                case Query_Builder::TYPE_DELETE:
                    $queryResult = $this->getQueryResult($query);

                    $cache['data'] = $queryResult->getResult();
                    Cache::invalidate(__CLASS__, $query->getInvalidateTags());
                    break;

                default:
                    Data_Source::getLogger()->fatal(['Unknown data source query statement type {$0}', $queryType], __FILE__, __LINE__, null, $query);
            }
        } catch (Exception $e) {
            Data_Source::getLogger()->fatal('Data source execute query failed', __FILE__, __LINE__, $e, $query);
        }

        if (!Environment::isProduction()) {
            $message = 'sql query: ' . str_replace("\t", '', str_replace("\n", ' ', $query->getSql())) . ' [' . implode(', ', $query->getBinds()) . '] ' . Logger::microtimeResult($startTime);

            if (Request::isCli()) {
                Query::getLogger()->info($message . ' ' . Memory::memoryGetUsagePeak(), Logger::SUCCESS, false);
            } else {
                Logger::fb($message);
            }
        }

        return $queryResult;
    }

    /**
     * Execute query command and return query result
     *
     * @param Query $query
     * @return Query_Result
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    private function getQueryResult(Query $query)
    {
        $queryCommand = 'execute' . ucfirst($query->getQueryType());
        return Query_Result::create($query->getModelClass(), $this->$queryCommand($this->getStatement($query), $query));
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
    public abstract function getStatement(Query $query);

    /**
     * Get current connected data scheme
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getScheme()
    {
        return $this->_scheme;
    }
}