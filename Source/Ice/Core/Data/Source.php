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
                    break;
                }
            }
        }

        if (empty($sourceDataProviderKey)) {
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
     * @author anonymous <email>
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public abstract function getColumns($tableName);

    /**
     * @param Query $query
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.2
     */
    public function execute(Query $query)
    {
        $queryCommand = 'execute' . ucfirst($query->getQueryType());

        $queryResultData = [];

        try {
            $queryResultData = $this->$queryCommand($this->getStatement($query), $query);
        } catch (Exception $e) {
            Data_Source::getLogger()->fatal('Data source execute query failed', __FILE__, __LINE__, $e, $query);
        }

        return $queryResultData;
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