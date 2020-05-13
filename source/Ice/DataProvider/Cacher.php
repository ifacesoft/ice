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
use Ice\Core\Exception;
use Ice\Exception\Error;
use Ice\Helper\Hash;

/**
 * Class String
 *
 * Data provider for cache
 *
 * @see \Ice\Core\DataProvider
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage DataProvider
 */
class Cacher extends DataProvider
{
    const DEFAULT_KEY = 'default';

    private $tag = '00000000';

    public function __construct($key, $index)
    {
        parent::__construct($key, $index);

        $tags = [];

        foreach ($this->getOptions()->gets('tagProviders') as $dataProvider => $name) {
            $tags = array_merge($tags, DataProvider::getInstance($dataProvider)->get($name, []));
        }

        $this->tag = Hash::get($tags, Hash::HASH_CRC32);
    }

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
     * @param null $default
     * @param bool $require
     * @return Cacheable
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.2
     * @since   0.0
     */
    public function get($key = null, $default = null, $require = false)
    {
        $cache = $this->getConnection()->get($this->getTag($key), $default, $require);

        return $cache ? $cache->validate() : $default;
    }

    /**
     * @return string
     */
    protected function getTag($key)
    {
        return $this->tag . DataProvider::KEY_DELIMETER . $key;
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
     * @param array $values
     * @param null $ttl
     * @return array
     *
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.2
     * @since   0.0
     */
    public function set(array $values = null, $ttl = null)
    {
        if ($ttl === -1) {
            return $values;
        }

        if ($ttl === null) {
            $ttl = $this->getOptions()->get('ttl', 3600);
        }

        foreach ($values as $key => $value) {
            $this->getConnection()->set([$this->getTag($key) => Cache::create($value, microtime(true)), $ttl]);
        }

        return $values;
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
        return $this->getConnection()->delete($this->getTag($key), $force);
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
        return $this->getConnection()->incr($this->getTag($key), $step);
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
        return $this->getConnection()->decr($this->getTag($key), $step);
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
     * @version 1.13
     * @since   0.0
     */
    protected function connect(&$connection)
    {
//        if (!Environment::isLoaded() || Environment::getInstance()->isDevelopment()) {
//            $dataProviderClass = Registry::class;
//        } else {
//            /**@var DataProvider $dataProviderClass */
//            $dataProviderClass = class_exists('Redis', false)
//                ? Redis::getClass()
//                : File::getClass();
//        }

        $dataProviderClass = Registry::class;

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

    /**
     * Set expire time (seconds)
     *
     * @param  $key
     * @param  int $ttl
     * @return mixed new value
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    public function expire($key, $ttl)
    {
        // TODO: Implement expire() method.
    }

    /**
     * Check for errors
     *
     * @return void
     *
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    function checkErrors()
    {
        // TODO: Implement checkErrors() method.
    }
}
