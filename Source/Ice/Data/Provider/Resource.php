<?php

namespace Ice\Data\Provider;

use Ice\Core\Data_Provider;
use Ice\Core\Exception;
use Ice\Core\Resource as Core_Resource;
use Ice\Helper\Object;

class Resource extends Data_Provider
{
    const DEFAULT_DATA_PROVIDER_KEY = 'Ice:Resource/default';
    const DEFAULT_KEY = 'Ice\Action\Test';

    private $_resourceKey = null;

    protected $_options = null;

    /**
     * Return resource key
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.5
     */
    public function getResourceKey()
    {
        if ($this->_resourceKey) {
            return $this->_resourceKey;
        }

        return $this->_resourceKey = Object::getName($this->getKey());
    }


    /**
     * Connect to data provider
     *
     * @param $connection
     * @return boolean
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.5
     */
    protected function connect(&$connection)
    {
        return $connection = ['resource' => [$this->getResourceKey() => Core_Resource::create($this->getKey())]];
    }

    /**
     * Close connection with data provider
     *
     * @param $connection
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.5
     */
    protected function close(&$connection)
    {
        // TODO: Implement close() method.
    }

    /**
     * Get data from data provider by key
     *
     * @param string $key
     * @return mixed
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.5
     */
    public function get($key = null)
    {
        return $key
            ? $this->getConnection()['resource'][$this->getResourceKey()]->get($key)
            : $this->getConnection();
    }

    /**
     * Return default data provider key
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.5
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
     * Set data to data provider
     *
     * @param string $key
     * @param $value
     * @param null $ttl
     * @return mixed setted value
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.5
     */
    public function set($key, $value, $ttl = null)
    {
        return $this->getConnection()['resource'][$this->getResourceKey()]->set($key, $value);
    }

    /**
     * Return instance of resource provider
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.5
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
     * @version 0.5
     * @since 0.5
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
     * @version 0.5
     * @since 0.5
     */
    public function incr($key, $step = 1)
    {
        // TODO: Implement incr() method.
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
     * @version 0.5
     * @since 0.5
     */
    public function decr($key, $step = 1)
    {
        // TODO: Implement decr() method.
    }

    /**
     * Flush all stored data
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.5
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
     * @version 0.5
     * @since 0.5
     */
    public function getKeys($pattern = null)
    {
        // TODO: Implement getKeys() method.
    }
}