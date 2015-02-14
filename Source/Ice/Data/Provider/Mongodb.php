<?php

namespace Ice\Data\Provider;

use Ice\Core\Data_Provider;
use Ice\Core\Exception;

class Mongodb extends Data_Provider
{
    const DEFAULT_DATA_PROVIDER_KEY = 'Ice:Mongodb/default';
    const DEFAULT_KEY = 'instance';

    /**
     * Return default data provider key
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    protected static function getDefaultDataProviderKey()
    {
        return self::DEFAULT_DATA_PROVIDER_KEY;
    }

    /**
     * Return default key
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    protected static function getDefaultKey()
    {
        return self::DEFAULT_KEY;
    }

    /**
     * Get data from data provider by key
     *
     * @param string $key
     * @return mixed
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    public function get($key = null)
    {
        // TODO: Implement get() method.
    }

    /**
     * Set data to data provider
     *
     * @param string $key
     * @param $value
     * @param null $ttl
     * @return mixed setted value
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    public function set($key, $value, $ttl = null)
    {
        // TODO: Implement set() method.
    }

    /**
     * Delete from data provider by key
     *
     * @param string $key
     * @param bool $force if true return boolean else deleted value
     * @throws Exception
     * @return mixed|boolean
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    public function delete($key, $force = true)
    {
        // TODO: Implement delete() method.
    }

    /**
     * Increment value by key with defined step (default 1)
     *
     * @param $key
     * @param int $step
     * @return mixed new value
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    public function incr($key, $step = 1)
    {
        // TODO: Implement inc() method.
    }

    /**
     * Decrement value by key with defined step (default 1)
     *
     * @param $key
     * @param int $step
     * @return mixed new value
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    public function decr($key, $step = 1)
    {
        // TODO: Implement dec() method.
    }

    /**
     * Flush all stored data
     *
     * @author anonymous <email>
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    public function flushAll()
    {
        // TODO: Implement flushAll() method.
    }

    /**
     * Return keys by pattern
     *
     * @param string $pattern
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    public function getKeys($pattern = null)
    {
        // TODO: Implement getKeys() method.
    }

    /**
     * Connect to data provider
     *
     * @param $connection
     * @return boolean
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    protected function connect(&$connection)
    {
        $options = $this->getOptions(__CLASS__);

        try {
            $connection = new \MongoClient('mongodb://' . $options['host'] . ':' . $options['port']);
        } catch (\MongoConnectionException $e) {
            Mongodb::getLogger()->exception('mongodb - ' . $e->getMessage(), __FILE__, __LINE__, $e);
        }

        return (bool)$connection;
    }

    /**
     * Return connection of mongodb
     *
     * @return \Mongo
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    public function getConnection()
    {
        return parent::getConnection();
    }

    /**
     * Close connection with data provider
     *
     * @param $connection
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    protected function close(&$connection)
    {
        // TODO: Implement close() method.
    }
}