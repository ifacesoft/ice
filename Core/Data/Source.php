<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 30.12.13
 * Time: 23:35
 */

namespace ice\core;

use ice\Exception;
use ice\Ice;

abstract class Data_Source
{
    const CONFIG_CACHE_DATA_PROVIDER = 'cacheDataProvider';
    const DEFAULT_CACHE_DATA_PROVIDER = 'Redis:data_source/';

    private static $_dataSources = array();

    private $_sourceDataProvider = null;
    private $_cacheDataProvider = null;

    private $_dataSourceKey = null;

    /**
     * @param Query $query
     * @throws Exception
     * @return array
     */
    abstract public function select(Query $query);

    /**
     * @param Query $query
     * @return array
     */
    abstract public function insert(Query $query);

    /**
     * @param Query $query
     * @return array
     */
    abstract public function update(Query $query);

    /**
     * @param Query $query
     * @return array
     */
    abstract public function delete(Query $query);


    private function __construct($dataSourceKey)
    {
        $this->_dataSourceKey = $dataSourceKey;
    }

    /**
     * @param Query $query
     * @param bool $isUseCache
     * @return Data
     */
    public function execute(Query $query, $isUseCache = true)
    {
        $statementType = $query->getStatementType();

        if ($statementType != 'select' || !$isUseCache) {
            return new Data($this->$statementType($query));
        }

        return new Data($this->$statementType($query));
    }

    /**
     * @return string
     */
    public function getDataSourceKey()
    {
        return $this->_dataSourceKey;
    }

    /**
     * @return Data_Provider
     */
    private function getCacheDataProvider()
    {
        if ($this->_cacheDataProvider !== null) {
            return $this->_cacheDataProvider;
        }

        $dataProviderKey = $this->getConfig()->getParam(
            $this->getDataSourceKey() . '/' . self::CONFIG_CACHE_DATA_PROVIDER
        );

        $this->_cacheDataProvider = isset($dataProviderKey)
            ? Data_Provider::getInstance($dataProviderKey)
            : Data_Provider::getInstance(self::DEFAULT_CACHE_DATA_PROVIDER);

        return $this->_cacheDataProvider;
    }

    /**
     * @return string
     */
    public function getScheme()
    {
        return $this->getSourceDataProvider()->getScheme();
    }

    /**
     * @return Data_Provider
     */
    private function getSourceDataProvider()
    {
        if ($this->_sourceDataProvider !== null) {
            return $this->_sourceDataProvider;
        }

        $this->_sourceDataProvider = Data_Provider::getInstance($this->getDataSourceKey());

        return $this->_sourceDataProvider;
    }

    public static function getDefault()
    {
        return self::get(Ice::getConfig()->getParam('defaultDataSourceKey'));
    }

    /**
     * @param $dataSourceKey // example: 'Mysqli:production/scheme'
     * @return Data_Source
     */
    public static function get($dataSourceKey)
    {
        $index = strstr($dataSourceKey, '/', true);

        if (isset(self::$_dataSources[$index])) {
            return self::$_dataSources[$index];
        }

        $dataSourceClass = 'Ice\data\source\\' . strstr($dataSourceKey, ':', true);

        self::$_dataSources[$index] = new $dataSourceClass($dataSourceKey);

        return self::$_dataSources[$index];
    }

    public static function getConfig()
    {
        return Config::get(get_called_class());
    }

    public function getConnection()
    {
        return $this->getSourceDataProvider()->getConnection();
    }
} 