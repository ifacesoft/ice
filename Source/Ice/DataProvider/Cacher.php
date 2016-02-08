<?php
/**
 * Ice data provider implementation string class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\DataProvider;

use Ice\Core\Cache;
use Ice\Core\Cacheable;
use Ice\Core\DataProvider;
use Ice\Core\Environment;
use Ice\Core\Exception;

/**
 * Class String
 *
 * Data provider for cache
 *
 * @see Ice\Core\DataProvider
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage DataProvider
 */
class Cacher extends DataProvider
{
    const DEFAULT_KEY = 'default';

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
     * Get data from data provider by key
     *
     * @param  string $key
     * @return Cacheable
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since   0.0
     */
    public function get($key = null)
    {
        /**
         * @var Cache $cache
         */
        if ($cache = $this->getConnection()->get($key)) {
            return $cache->validate();
        }

        return null;
    }

    /**
     * Get instance connection of data provider
     *
     * @return DataProvider
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
     * Set data to data provider
     *
     * @param  string $key
     * @param  $value
     * @param  null $ttl
     * @return mixed setted value
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function set($key, $value = null, $ttl = null)
    {
        if (is_array($key) && $value === null) {
            foreach ($key as $index => $value) {
                $this->set($index, $value, $ttl);
            }

            return $key;
        }

        if ($ttl == -1) {
            return $value;
        }

        if ($ttl === null) {
            $options = $this->getOptions();
            $ttl = isset($options['ttl']) ? $options['ttl'] : 3600;
        }

        $this->getConnection()->set($key, Cache::create($value, microtime(true)), $ttl);

        return $value;
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
        return $this->getConnection()->delete($key, $force);
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
     * @version 0.0
     * @since   0.0
     */
    public function incr($key, $step = 1)
    {
        return $this->getConnection()->incr($key, $step);
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
     * @version 0.0
     * @since   0.0
     */
    public function decr($key, $step = 1)
    {
        return $this->getConnection()->decr($key, $step);
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
        return $this->getConnection()->flushAll();
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
        return $this->getConnection()->getKeys($pattern);
    }

    /**
     * Connect to data provider
     *
     * @param  $connection
     * @return boolean
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since   0.0
     */
    protected function connect(&$connection)
    {
        if (!Environment::isLoaded() || Environment::getInstance()->isDevelopment()) {
            return $connection = Registry::getInstance($this->getKey(), $this->getIndex());
        }

        if (!Environment::getInstance()->isProduction()) {
            return $connection = File::getInstance($this->getKey(), $this->getIndex());
        }

        /**
         * @var DataProvider $dataProviderClass
         */
        $dataProviderClass = class_exists('Redis', false)
            ? Redis::getClass()
            : File::getClass();

        return $connection = $dataProviderClass::getInstance($this->getKey(), $this->getIndex());
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
        $connection = null;
        return true;
    }
}
