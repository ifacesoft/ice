<?php
/**
 * Ice data provider implementation redis class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\DataProvider;

use Ice\Core\DataProvider;
use Ice\Core\Exception;
use Ice\Core\Logger;
use Ice\Exception\Error;

/**
 * Class Redis
 *
 * Data provider for redis storage
 *
 * @see Ice\Core\DataProvider
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage DataProvider
 *
 * @version 0.0
 * @since   0.0
 */
class Redis extends DataProvider
{
    const DEFAULT_KEY = 'default';

    protected $options = [
        'host' => 'localhost',
        'port' => 6379
    ];

    /**
     * Return default key
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
     */
    protected static function getDefaultKey()
    {
        return self::DEFAULT_KEY;
    }

    /**
     * Set data to data provider
     *
     * @param array $values
     * @param  null $ttl
     * @return array
     * @throws Error
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.2
     * @since   0.0
     */
    public function set(array $values = null, $ttl = null)
    {
        if ($ttl == -1) {
            return $values;
        }

        if ($ttl === null) {
            $options = $this->getOptions();
            $ttl = isset($options['ttl']) ? $options['ttl'] : 3600;
        }

        foreach ($values as $key => $value) {
            if (!$this->getConnection()->set($this->getFullKey($key), $value, $ttl)) {
                throw new Error(['{$0} set  param {$1} fail', [__CLASS__, $key]]);
            }
        }

        return $values;
    }

    /**
     * Return data provider redis connection
     *
     * @return \Redis
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function getConnection()
    {
        return parent::getConnection();
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
     * @version 0.0
     * @since   0.0
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
     * @param  string $key
     * @param null $default
     * @param bool $require
     * @return mixed
     * @throws Error
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.2
     * @since   0.0
     */
    public function get($key = null, $default = null, $require = false)
    {
        $value = $this->getConnection()->get($this->getFullKey($key));

        if ($value === false) {
            $value = $default;
        }

        if ($value === null && $require) {
            throw new Error(['Param {$0} from data provider {$1} is require', ['key', __CLASS__]]);
        }

        return $value;
    }

    /**
     * Increment value by key with defined step (default 1)
     *
     * @param  $key
     * @param  int $step
     * @throws Exception
     * @return mixed new value
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.0
     */
    public function incr($key, $step = 1)
    {
        return $this->getConnection()->incrBy($this->getFullKey($key), $step);
    }

    /**
     * Decrement value by key with defined step (default 1)
     *
     * @param  $key
     * @param  int $step
     * @throws Exception
     * @return mixed new value
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.0
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
     * @since   0.0
     */
    public function flushAll()
    {
        $this->getConnection()->delete($this->getConnection()->getKeys($this->getKeyPrefix() . '*'));
    }

    /**
     * Return keys by pattern
     *
     * @param  string $pattern
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function getKeys($pattern = null)
    {
        $keyPrefix = $this->getKeyPrefix() . DataProvider::PREFIX_KEY_DELIMETER;

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
     * @param  $connection
     * @return boolean
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    protected function connect(&$connection)
    {
        $options = $this->getOptions();

        $connection = new \Redis();

        $isConnected = $connection->connect($options['host'], $options['port']);

        if (!$isConnected) {
            Logger::getInstance(__CLASS__)
                ->exception('redis - ' . $this->getConnection()->getLastError(), __FILE__, __LINE__);
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
     * @param  $connection
     * @return boolean
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    protected function close(&$connection)
    {
        $this->getConnection()->close();
        return true;
    }
}
