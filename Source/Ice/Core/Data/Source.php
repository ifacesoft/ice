<?php
/**
 * Ice core data source container abstract class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

use Ice\Core;

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
    use Stored;

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

        return substr(strstr($dataSourceClass::getDefaultClassKey(), '/'), 1);
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
    public static function getDefaultClassKey()
    {
        /** @var Data_Source $dataSourceClass */
        $dataSourceClass = self::getClass();

        $key = 'defaultClassKey_' . $dataSourceClass;
        $repository = Data_Source::getRepository();

        if ($defaultClassKey = $repository->get($key)) {
            return $defaultClassKey;
        }

        if ($dataSourceClass == __CLASS__) {
            $dataSourceClass = Mysqli::getClass();
        }

        $schemes = Data_Source::getConfig()->gets($dataSourceClass . '/default');

        return $repository->set($key, $dataSourceClass . '/default.' . reset($schemes), 0);
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
        if (!strpos($key, '.')) {
            Data_Source::getLogger()->exception(['Data source name not valid {$0}', $key], __FILE__, __LINE__);
        }

        list($key, $scheme) = explode('.', $key);

        $dataSourceClass = self::getClass();
        return new $dataSourceClass($key, $scheme);
    }

    /**
     * Execute query select to data source
     *
     * @param Query $query
     * @return array
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since 0
     */
    abstract public function executeSelect(Query $query);

    /**
     * Execute query insert to data source
     *
     * @param Query $query
     * @return array
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since 0
     */
    abstract public function executeInsert(Query $query);

    /**
     * Execute query update to data source
     *
     * @param Query $query
     * @return array
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since 0
     */
    abstract public function executeUpdate(Query $query);

    /**
     * Execute query update to data source
     *
     * @param Query $query
     * @return array
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since 0
     */
    abstract public function executeDelete(Query $query);

    /**
     * Execute query create table to data source
     *
     * @param Query $query
     * @return array
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since 0
     */
    abstract public function executeCreate(Query $query);

    /**
     * Execute query drop table to data source
     *
     * @param Query $query
     * @return array
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since 0
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
     * @since 0.0
     */
    public function getConnection()
    {
        return $this->getSourceDataProvider()->getConnection();
    }

    /**
     * Get data Scheme from data source
     *
     * @param Module $module
     * @return array
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since 0
     */
    public abstract function getTables(Module $module);

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
     * Get table references from source
     *
     * @param $tableName
     * @return array
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since 0
     */
    public abstract function getReferences($tableName);

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
        $startTime = Profiler::getMicrotime();
        $startMemory = Profiler::getMemoryGetUsage();

        $queryResult = null;

        $queryCommand = 'execute' . ucfirst($query->getQueryType());

        try {
            $queryType = $query->getQueryType();
            if (
                ($queryType == Query_Builder::TYPE_SELECT && !$ttl) ||
                $queryType == Query_Builder::TYPE_CREATE ||
                $queryType == Query_Builder::TYPE_DROP
            ) {
                $queryResult = Query_Result::create($query, $this->$queryCommand($query));
                Profiler::setPoint($queryResult->__toString(), $startTime, $startMemory);
                Data_Source::getLogger()->log(['(not cache) {$0}', Profiler::getReport($queryResult->__toString())], Logger::INFO);
                return $queryResult;
            }

            switch ($queryType) {
                case Query_Builder::TYPE_SELECT:
                    $cacher = Query_Result::getCacher();
                    $queryHash = $query->getFullHash();

                    if ($queryResult = $cacher->get($queryHash)) {
                        Profiler::setPoint($queryResult->__toString(), $startTime, $startMemory);
                        Data_Source::getLogger()->log(['(cache) {$0}', Profiler::getReport($queryResult->__toString())], Logger::INFO);
                        return $queryResult;
                    }

                    $queryResult = Query_Result::create($query, $this->$queryCommand($query));
                    Profiler::setPoint($queryResult->__toString(), $startTime, $startMemory);
                    Data_Source::getLogger()->log(['(new) {$0}', Profiler::getReport($queryResult->__toString())], Logger::SUCCESS);
                    $cacher->set($queryHash, $queryResult, $ttl);
                    break;

                case Query_Builder::TYPE_INSERT:
                case Query_Builder::TYPE_UPDATE:
                case Query_Builder::TYPE_DELETE:
                    $queryResult = Query_Result::create($query, $this->$queryCommand($query))->invalidate();
                Profiler::setPoint($queryResult->__toString(), $startTime, $startMemory);
                Data_Source::getLogger()->log(['(new) {$0}', Profiler::getReport($queryResult->__toString())], Logger::SUCCESS);
                    return $queryResult;

                default:
                    Data_Source::getLogger()->exception(['Unknown data source query statement type {$0}', $queryType], __FILE__, __LINE__, null, $query);
            }
        } catch (\Exception $e) {
            Data_Source::getLogger()->log(print_r($query->getBody(), true) . ' (' .  print_r($query->getBinds(), true) . ')', Logger::DANGER);
            Data_Source::getLogger()->log($e->getMessage(), Logger::DANGER);
            Data_Source::getLogger()->exception('Data source execute query failed', __FILE__, __LINE__, $e, $query);
        }

        return null;
    }

    /**
     * Prepare query statement for query
     *
     * @param $body
     * @param array $binds
     * @return mixed
     * @author anonymous <email>
     *
     * @version 0
     * @since 0
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
     * @since 0
     */
    abstract public function getQueryTranslatorClass();

    /**
     * Begin transaction
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since 0
     */
    abstract public function beginTransaction();

    /**
     * Commit transaction
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since 0
     */
    abstract public function commitTransaction();

    /**
     * Rollback transaction
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since 0
     */
    abstract public function rollbackTransaction();

    /**
     * Return data source key
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.5
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
     * @since 0.5
     */
    public function getKey()
    {
        return $this->_key;
    }

    /**
     * Return current scheme
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.5
     */
    public function getScheme()
    {
        return $this->_scheme;
    }
}