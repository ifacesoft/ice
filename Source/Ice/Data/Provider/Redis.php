<?php
/**
 * Ice data provider implementation redis class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Data\Provider;

use Ice\Core\Data_Provider;
use Ice\Core\Exception;

/**
 * Class Redis
 *
 * Data provider for redis storage
 *
 * @see Ice\Core\Data_Provider
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Data_Provider
 *
 * @version 0.0
 * @since 0.0
 */
class Redis extends Data_Provider
{
    const DEFAULT_DATA_PROVIDER_KEY = 'Ice:Redis/default';
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
     * @version 0.4
     * @since 0.4
     */
    protected static function getDefaultKey()
    {
        return self::DEFAULT_KEY;
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
     */
    public function set($key, $value, $ttl = null)
    {
        if ($ttl == -1) {
            return $value;
        }

        if ($ttl === null) {
            $options = $this->getOptions(__CLASS__);
            $ttl = $options['ttl'];
        }

        return $this->getConnection()->set($this->getFullKey($key), $value, $ttl) ? $value : null;
    }

    /**
     * Return connection options
     *
     * @param $class
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    protected function getOptions($class)
    {
        return array_merge(['host' => 'localhost', 'port' => 6379], parent::getOptions($class));
    }

    /**
     * Return data provider redis connection
     *
     * @return \Redis
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getConnection()
    {
        return parent::getConnection();
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
     * @version 0.0
     * @since 0.0
     */
    public function delete($key, $force = true)
    {
        if ($force) {
            $this->getConnection()->delete($this->getFullKey($key));
            return true;
        }

        $value = $this->get($key);

        $this->getConnection()->delete($this->getFullKey($key));

        return $value;
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
     * @since 0.0
     */
    public function get($key = null)
    {
        $value = $this->getConnection()->get($this->getFullKey($key));
        return $value === false ? null : $value;
    }

    /**
     * Increment value by key with defined step (default 1)
     *
     * @param $key
     * @param int $step
     * @throws Exception
     * @return mixed new value
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.0
     */
    public function incr($key, $step = 1)
    {
        return $this->getConnection()->incrBy($this->getFullKey($key), $step);
    }

    /**
     * Decrement value by key with defined step (default 1)
     *
     * @param $key
     * @param int $step
     * @throws Exception
     * @return mixed new value
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.0
     */
    public function decr($key, $step = 1)
    {
        return $this->getConnection()->decrBy($this->getFullKey($key), $step);
    }

    /**
     * Flush all stored data
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function flushAll()
    {
        $this->getConnection()->delete($this->getConnection()->getKeys($this->getKeyPrefix() . '*'));
    }

    /**
     * Return keys by pattern
     *
     * @param string $pattern
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getKeys($pattern = null)
    {
        $keyPrefix = $this->getKeyPrefix() . Data_Provider::PREFIX_KEY_DELIMETER;

        $size = strlen($keyPrefix);

        $keys = [];

        foreach ($this->getConnection()->getKeys($keyPrefix . $pattern . '*') as $key) {
            $keys[] = substr($key, $size);
        }

        return $keys;
    }

    /**
     * Connect to data provider
     *
     * @param $connection
     * @return boolean
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    protected function connect(&$connection)
    {
        $options = $this->getOptions(__CLASS__);

        $connection = new \Redis();

        $isConnected = $connection->connect($options['host'], $options['port']);

        if (!$isConnected) {
            Mysqli::getLogger()->exception('redis - ' . $this->getConnection()->getLastError(), __FILE__, __LINE__);
        }

        if (function_exists('igbinary_serialize')) {
            $connection->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_IGBINARY);
        } else {
            $connection->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_PHP);
        }

        $connection->setOption(\Redis::OPT_PREFIX, 'ice/');

        return $isConnected;

    }

    /**
     * Close connection with data provider
     *
     * @param $connection
     * @return boolean
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    protected function close(&$connection)
    {
        $this->getConnection()->close();
        return true;
    }
}