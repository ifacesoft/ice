<?php
/**
 * Ice data provider implementation registry class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\DataProvider;

use ArrayObject;
use Ice\Core\DataProvider;
use Ice\Core\Exception;
use Ice\Exception\Error;

/**
 * Class Registry
 *
 * Data provider for registry data
 *
 * @see Ice\Core\DataProvider
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage DataProvider
 */
class Registry extends DataProvider
{
    const DEFAULT_KEY = 'default';

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
     * @since   0.0
     */
    public function delete($key, $force = true)
    {
        $keyPrefix = $this->getKeyPrefix();
        $data = $this->getConnection()->$keyPrefix;

        if ($force) {
            unset($data[$key]);
            $this->getConnection()->$keyPrefix = $data;
            return true;
        }

        $value = $data[$key];

        unset($data[$key]);
        $this->getConnection()->$keyPrefix = $data;

        return $value;
    }

    /**
     * Return registry connection
     *
     * @return ArrayObject
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
     * Increment value by key with defined step (default 1)
     *
     * @param  $key
     * @param  int $step
     * @throws Exception
     * @return mixed new value
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @todo    0.4 need refactoring // long time execute
     * @version 0.4
     * @since   0.0
     */
    public function incr($key, $step = 1)
    {
        return $this->set($key, $this->get($key) + $step);
    }

    /**
     * Set data to data provider
     *
     * @param array $values
     * @param  integer $ttl
     * @return array
     *
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

        foreach ($values as $key => $value) {
            $keyPrefix = $this->getKeyPrefix();

            if (isset($this->getConnection()->$keyPrefix)) {
                $data = $this->getConnection()->$keyPrefix;
                $data[$key] = $value;
                $this->getConnection()->$keyPrefix = $data;
            } else {
                $this->getConnection()->$keyPrefix = [$key => $value];
            }
        }

        return $values;
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
        $keyPrefix = $this->getKeyPrefix();

        if (!isset($this->getConnection()->$keyPrefix)) {
            return $key === null ? [] : $default;
        }

        $data = $this->getConnection()->$keyPrefix;

        if ($key === null) {
            return empty($data) ? [] : $data;
        }

        $value = array_key_exists($key, $data) ? $data[$key] : $default;

        if ($value === null && $require) {
            throw new Error(['Param {$0} from data provider {$1} is require', ['key', __CLASS__]]);
        }

        return $value;
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
     * @todo    0.4 need refactoring // long time execute
     * @version 0.0
     * @since   0.0
     */
    public function decr($key, $step = 1)
    {
        return $this->set($key, $this->get($key) - $step);
    }

    /**
     * Flush all stored data
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.0
     */
    public function flushAll()
    {
        $keyPrefix = $this->getKeyPrefix();
        $this->getConnection()->$keyPrefix = [];
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
        return array_keys($this->get());
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
        $connection = new ArrayObject();
        return true;
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
