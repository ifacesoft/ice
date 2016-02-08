<?php
/**
 * Ice data provider implementation request class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Data\Provider;

use Ice\Core\DataProvider;
use Ice\Core\Exception;

/**
 * Class Request
 *
 * Data provider for request data
 *
 * @see Ice\Core\DataProvider
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage DataProvider
 */
class Request extends DataProvider
{
    const DEFAULT_KEY = 'default';

    /**
     * Return default data provider key
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
     * Set data to data provider
     *
     * @param  string $key
     * @param  $value
     * @param  null $ttl
     * @throws Exception
     * @return mixed setted value
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
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

        return $_REQUEST[$key] = $value;
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
        if ($force) {
            unset($_REQUEST[$key]);
            return true;
        }

        $value = $this->get($key);

        unset($_REQUEST[$key]);

        return $value;
    }

    /**
     * Get data from data provider by key
     *
     * @param  string $key
     * @return array|mixed|null
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.0
     */
    public function get($key = null)
    {
        if (empty($key)) {
            return $_REQUEST;
        }

        if (!is_array($key)) {
            return isset($_REQUEST[$key]) ? $_REQUEST[$key] : null;
        }

        $params = [];

        foreach ($key as $name) {
            $params[$name] = isset($_REQUEST[$name]) ? $_REQUEST[$name] : null;
        }

        return $params;
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
        return $_REQUEST[$key] += $step;
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
        return $_REQUEST[$key] -= $step;
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
        $_REQUEST = [];
    }

    /**
     * Return keys by pattern
     *
     * @param  string $pattern
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @todo    0.4 implements filter by pattern
     * @version 0.4
     * @since   0.0
     */
    public function getKeys($pattern = null)
    {
        return array_keys($_REQUEST);
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
     * @since   0.0
     */
    protected function connect(&$connection)
    {
        return isset($_REQUEST);
    }

    /**
     * Close connection with data provider
     *
     * @param  $connection
     * @return boolean
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.0
     */
    protected function close(&$connection)
    {
        unset($_REQUEST);
        return true;
    }
}
