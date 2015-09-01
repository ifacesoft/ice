<?php

namespace Ice\Data\Provider;

use Ice\Core\Data_Provider;
use Ice\Core\Exception;
use Ice\Core\Logger;

class Mongodb extends Data_Provider
{
    const DEFAULT_DATA_PROVIDER_KEY = 'Ice:Mongodb/default';
    const DEFAULT_KEY = 'default';

    protected $options = [
        'host' => 'localhost',
        'port' => '27017'
    ];

    /**
     * Return default data provider key
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
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
     * @since   0.0
     */
    protected static function getDefaultKey()
    {
        return self::DEFAULT_KEY;
    }

    /**
     * Get data from data provider by key
     *
     * @param  string $key
     * @return mixed
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
     */
    public function get($key = null)
    {
        // TODO: Implement get() method.
    }

    /**
     * Set data to data provider
     *
     * @param  string $key
     * @param  $value
     * @param  null $ttl
     * @return mixed setted value
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
     */
    public function set($key, $value = null, $ttl = null)
    {
        // TODO: Implement set() method.
    }

    /**
     * Delete from data provider by key
     *
     * @param  string $key
     * @param  bool $force if true return boolean else deleted value
     * @throws Exception
     * @return mixed|boolean
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
     */
    public function delete($key, $force = true)
    {
        // TODO: Implement delete() method.
    }

    /**
     * Increment value by key with defined step (default 1)
     *
     * @param  $key
     * @param  int $step
     * @return mixed new value
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
     */
    public function incr($key, $step = 1)
    {
        // TODO: Implement inc() method.
    }

    /**
     * Decrement value by key with defined step (default 1)
     *
     * @param  $key
     * @param  int $step
     * @return mixed new value
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
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
     * @since   0.4
     */
    public function flushAll()
    {
        // TODO: Implement flushAll() method.
    }

    /**
     * Return keys by pattern
     *
     * @param  string $pattern
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
     */
    public function getKeys($pattern = null)
    {
        // TODO: Implement getKeys() method.
    }

    /**
     * Return connection of mongodb
     *
     * @return \Mongo
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
     */
    public function getConnection()
    {
        return parent::getConnection();
    }

    /**
     * Connect to data provider
     *
     * @param  $connection
     * @return boolean
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
     */
    protected function connect(&$connection)
    {
        $options = $this->getOptions();

        try {
            $connection = new \MongoClient('mongodb://' . $options['host'] . ':' . $options['port']);
        } catch (\Exception $e) {
            Mongodb::getLogger()->info(['mongodb - #' . $e->getCode() . ': {$0}', $e->getMessage()], Logger::WARNING);
            return false;
        }

        return (bool)$connection;
    }

    /**
     * Close connection with data provider
     *
     * @param $connection
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
     */
    protected function close(&$connection)
    {
        // TODO: Implement close() method.
    }
}
