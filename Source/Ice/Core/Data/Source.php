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

        $cache = [
            'tags' => $query->getValidateTags(),
            'time' => 0,
            'data' => [],
            'queryBody' => null,
            'queryParams' => []
        ];

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
                        Data_Source::getLogger()->log([
                            'sql cache: {$0} [{$1}] {$2}',
                            [
                                print_r($cache['queryBody'], true),
                                implode(', ', $cache['queryParams']),
                                Logger::microtimeResult($startTime)
                            ]
                        ], Logger::INFO);

                        return Query_Result::create($query->getModelClass(), $cache['data']);
                    }

                    $queryResult = $this->getQueryResult($query);

                    $cache['data'] = $queryResult->getResult();
                    $cache['queryBody'] = $queryResult->getQueryBody();
                    $cache['queryParams'] = $queryResult->getQueryParams();
                    $cache['time'] = time();

                    $cacheDataProvider->set($hash, $cache, $ttl);
                    break;

                case Query_Builder::TYPE_INSERT:
                case Query_Builder::TYPE_UPDATE:
                case Query_Builder::TYPE_DELETE:
                    $queryResult = $this->getQueryResult($query);

                $cache['data'] = $queryResult->getResult();
                $cache['queryBody'] = $queryResult->getQueryBody();
                $cache['queryParams'] = $queryResult->getQueryParams();
                    Cache::invalidate(__CLASS__, $query->getInvalidateTags());
                    break;

                default:
                    Data_Source::getLogger()->fatal(['Unknown data source query statement type {$0}', $queryType], __FILE__, __LINE__, null, $query);
            }
        } catch (Exception $e) {
            Data_Source::getLogger()->log([
                'query error: {$0} [{$1}] {$2}',
                [
                    print_r($cache['queryBody'], true),
                    implode(', ', $cache['queryParams']),
                    Logger::microtimeResult($startTime)
                ]
            ], Logger::DANGER);

            Data_Source::getLogger()->fatal('Data source execute query failed', __FILE__, __LINE__, $e, $query);
        }

        Data_Source::getLogger()->log([
            'query: {$0} [{$1}] {$2}',
            [
                print_r($cache['queryBody'], true),
                implode(', ', $cache['queryParams']),
                Logger::microtimeResult($startTime)
            ]
        ]);

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

        $data = $this->$queryCommand($query);

        if (empty($data)) {
            Data_Source::getLogger()->fatal(
                [
                    'Failed creating result of query \'{$0}\' [$1] with data source {$2}',
                    [$data['queryBody'], implode(', ', $data['queryParams']), $query->getDataSourceKey()]
                ], __FILE__, __LINE__, null, $query
            );
        }

        return Query_Result::create($query->getModelClass(), $data);
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
}