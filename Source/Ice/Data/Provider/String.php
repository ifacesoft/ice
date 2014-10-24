<?php
/**
 * Ice data provider implementation string class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Data\Provider;

use Ice\Core\Data_Provider;
use Ice\Core\Exception;

/**
 * Class String
 *
 * Data provider for string cache
 *
 * @see Ice\Core\Data_Provider
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Data_Provider
 *
 * @version stable_0
 * @since stable_0
 */
class String extends Data_Provider
{
    /**
     * Get data from data provider by key
     *
     * @param string  $key
     * @return mixed
     */
    public function get($key = null)
    {
        return $this->getConnection()->get($key);
    }

    /**
     * Get instance connection of data provider
     *
     * @return Data_Provider
     */
    public function getConnection()
    {
        return parent::getConnection();
    }

    /**
     * Set data to data provider
     *
     * @param string $key
     * @param $value
     * @param null $ttl
     * @return mixed setted value
     */
    public function set($key, $value, $ttl = null)
    {
        if ($ttl == -1) {
            return $value;
        }

        return $this->getConnection()->set($key, $value, $ttl);
    }

    /**
     * Delete from data provider by key
     *
     * @param string $key
     * @param bool $force if true return boolean else deleted value
     * @throws Exception
     * @return mixed|boolean
     */
    public function delete($key, $force = true)
    {
        return $this->getConnection()->delete($key, $force);
    }

    /**
     * Increment value by key with defined step (default 1)
     *
     * @param $key
     * @param int $step
     * @return mixed new value
     */
    public function inc($key, $step = 1)
    {
        return $this->getConnection()->inc($key, $step);
    }

    /**
     * Decrement value by key with defined step (default 1)
     *
     * @param $key
     * @param int $step
     * @return mixed new value
     */
    public function dec($key, $step = 1)
    {
        return $this->getConnection()->dec($key, $step);
    }

    /**
     * Flush all stored data
     */
    public function flushAll()
    {
        return $this->getConnection()->flushAll();
    }

    /**
     * Return keys by pattern
     *
     * @param string $pattern
     * @return array
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
     */
    protected function connect(&$connection)
    {
        $dataProviderClass = class_exists('Redis', false)
            ? Redis::getClass()
            : File::getClass();

        return $connection = new $dataProviderClass($this->getKey(), $this->getIndex());
    }

    /**
     * Close connection with data provider
     *
     * @param $connection
     * @return boolean
     */
    protected function close(&$connection)
    {
        $connection = null;
        return true;
    }
}