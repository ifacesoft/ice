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
     * @version 1.3
     * @since   0.0
     */
    public function delete($key, $force = true)
    {
        if (empty($key)) {
            return is_array($key) ? [] : null;
        }

        $connection = $this->getConnection();

        $keyPrefix = $this->getKeyPrefix();

        $data = $connection->offsetGet($keyPrefix);

        if (empty($data)) {
            return is_array($key) ? [] : null;
        }

        $values = [];

        foreach ((array)$key as $k) {
            $values[$k] = !$force && array_key_exists($k, $data)
                ? $data[$k]
                : null;

            unset($data[$k]);
        }

        $connection->offsetSet($keyPrefix, $data);


        return is_array($key) ? $values : reset($values);
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
     * @version 1.3
     * @since   0.0
     */
    public function set(array $values = null, $ttl = null)
    {
        if ($ttl == -1) {
            return $values;
        }

        $connection = $this->getConnection();

        $keyPrefix = $this->getKeyPrefix();

        $data = $connection->offsetGet($keyPrefix);

        foreach ($values as $key => $value) {
            $data[$key] = $value;
        }

        $connection->offsetSet($keyPrefix, $data);

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
     * @version 1.3
     * @since   0.0
     */
    public function get($key = null, $default = null, $require = false)
    {
        $connection = $this->getConnection();

        $keyPrefix = $this->getKeyPrefix();

        $data = $connection->offsetGet($keyPrefix);

        if (empty($key)) {
            return $data;
        }

        if (empty($data)) {
            return is_array($key) ? (array)$default : $default;
        }

        $values = [];

        foreach ((array)$key as $k) {
            $values[$k] = array_key_exists($k, $data)
                ? $data[$k]
                : null;

            if ($values[$k] === null) {
                if (is_array($default)) {
                    $values[$k] = array_key_exists($k, $default) ? $default[$k] : null;
                } else {
                    $values[$k] = $default;
                }
            }

            if ($require && ($values[$k] === null || $values[$k] === '')) {
                $dataProviderClass = get_class($this);

                throw new Error(['Param {$0} from data provider {$1} is require', [$k, $dataProviderClass]]);
            }
        }

        return is_array($key) ? $values : reset($values);
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
     * @version 1.3
     * @since   0.0
     */
    public function flushAll()
    {
        $this->getConnection()->offsetSet($this->getKeyPrefix(), []);
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
     * @version 1.3
     * @since   0.0
     */
    protected function connect(&$connection)
    {
        $connection = new ArrayObject([$this->getKeyPrefix() => []]);

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
