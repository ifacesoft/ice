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
use Ice\Helper\Arrays;

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
     * Data source scheme
     *
     * @var string
     */
    protected $_scheme = null;
    /**
     * Data source key
     *
     * @var string
     */
    private $_key = null;

    /**
     * Private constructor for dat source
     *
     * @param $key
     * @param $scheme
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.0
     */
    private function __construct($key, $scheme)
    {
        $this->_key = $key;
        $this->_scheme = $scheme;
        $this->getSourceDataProvider()->setScheme($this->_scheme);
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
        $sourceDataProviderClass = $this->getDataProviderClass();

        /** @var Data_Provider $sourceDataProvider */
        $sourceDataProvider = $sourceDataProviderClass::getInstance($this->_key);

        if ($sourceDataProvider->getScheme() != $this->_scheme) {
            $sourceDataProvider->setScheme($this->_scheme);
        }

        return $sourceDataProvider;
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
    abstract public function getDataProviderClass();

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
     * Default key of data source
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.0
     */
    protected static function getDefaultKey()
    {
        /** @var Data_Source $dataSourceClass */
        $dataSourceClass = self::getClass();

        $key = 'defaultKey_' . $dataSourceClass;
        $repository = Data_Source::getRepository();

        if ($defaultKey = $repository->get($key)) {
            return $defaultKey;
        }

        return $repository->set($key, substr(strstr($dataSourceClass::getDefaultClassKey(), '/'), 1), 0);
    }

    /**
     * Return default class key
     *
     * @return string
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    protected static function getDefaultClassKey()
    {
        /** @var Data_Source $dataSourceClass */
        $dataSourceClass = self::getClass();

        $key = 'defaultClassKey_' . $dataSourceClass;
        $repository = Data_Source::getRepository();

        if ($defaultClassKey = $repository->get($key)) {
            return $defaultClassKey;
        }

        $defaultConfig = Data_Source::getConfig()->gets('default');

        if ($dataSourceClass == __CLASS__) {
            list($dataSourceClass, $schemes) = each($defaultConfig);
        } else {
            $schemes = $defaultConfig[$dataSourceClass];
        }

        $schemes = (array)$schemes;

        return $repository->set($key, $dataSourceClass . '/default.' . reset($schemes), 0);
    }

    /**
     * Return default class
     *
     * @return Data_Source
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    protected static function getDefaultClass()
    {
        /** @var Data_Source $dataSourceClass */
        $dataSourceClass = self::getClass();

        $key = 'defaultClass_' . $dataSourceClass;
        $repository = Data_Source::getRepository();

        if ($defaultClass = $repository->get($key)) {
            return $defaultClass;
        }

        return $repository->set($key, strstr($dataSourceClass::getDefaultClassKey(), '/', true), 0);
    }

    /**
     * Create new instance of data source
     *
     * @param $key
     * @return Data_Source
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.0
     */
    protected static function create($key)
    {
        list($key, $scheme) = explode('.', $key);

        $schemes = Data_Source::getConfig()->gets($key . '/' . self::getClass(), false);

        if (empty($schemes) || !in_array($scheme, $schemes)) {
            Data_Source::getLogger()->fatal(['Data source not found for scheme {$0}', $key], __FILE__, __LINE__);
        }

        $dataSourceClass = self::getClass();
        return new $dataSourceClass($key, $scheme);
    }

    /**
     * Execute query select to data source
     *
     * @param mixed $statement
     * @param Query $query
     * @return array
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since 0
     */
    abstract public function executeSelect($statement, Query $query);

    /**
     * Execute query insert to data source
     *
     * @param mixed $statement
     * @param Query $query
     * @return array
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since 0
     */
    abstract public function executeInsert($statement, Query $query);

    /**
     * Execute query update to data source
     *
     * @param mixed $statement
     * @param Query $query
     * @return array
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since 0
     */
    abstract public function executeUpdate($statement, Query $query);

    /**
     * Execute query update to data source
     *
     * @param mixed $statement
     * @param Query $query
     * @return array
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since 0
     */
    abstract public function executeDelete($statement, Query $query);

    /**
     * Execute query create table to data source
     *
     * @param $statement
     * @param Query $query
     * @return array
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since 0
     */
    abstract public function executeCreate($statement, Query $query);

    /**
     * Execute query drop table to data source
     *
     * @param mixed $statement
     * @param Query $query
     * @return array
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since 0
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
     * @author anonymous <email>
     *
     * @version 0
     * @since 0
     */
    public abstract function getColumns($tableName);

    /**
     * Get table indexes from source
     *
     * @param $tableName
     * @return array
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since 0
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
                        Data_Source::getLogger()->log('sql cache: ' . str_replace("\t", '', str_replace("\n", ' ', $query->getSql())) . ' [' . implode(', ', $query->getBinds()) . '] ' . Logger::microtimeResult($startTime));

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
            Data_Source::getLogger()->log('sql error: ' . str_replace("\t", '', str_replace("\n", ' ', $query->getSql())) . ' [' . implode(', ', $query->getBinds()) . '] ' . Logger::microtimeResult($startTime));
            Data_Source::getLogger()->fatal('Data source execute query failed', __FILE__, __LINE__, $e, $query);
        }

        Data_Source::getLogger()->log('sql query: ' . str_replace("\t", '', str_replace("\n", ' ', $query->getSql())) . ' [' . implode(', ', $query->getBinds()) . '] ' . Logger::microtimeResult($startTime));

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
     * @author anonymous <email>
     *
     * @version 0
     * @since 0
     */
    abstract public function getStatement(Query $query);

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
    abstract public function getQueryTranslatorClass();
}