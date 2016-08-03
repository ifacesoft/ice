<?php
/**
 * Ice data provider implementation request class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\DataProvider;

use Ice\Core\DataProvider;
use Ice\Core\Exception;
use Ice\Exception\Error;

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
     * @param array $values
     * @param  null $ttl
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
            $_REQUEST[$key] = $value;
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
     * @param null $default
     * @param bool $require
     * @return array|mixed|null
     * @throws Error
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.2
     * @since   0.0
     */
    public function get($key = null, $default = null, $require = false)
    {
        if (empty($key)) {
            return $_REQUEST;
        }

        if (is_array($key)) {
            return array_intersect_key($_REQUEST, array_flip($key));
        }

        $value = array_key_exists($key, $_REQUEST) ? $_REQUEST[$key] : $default;

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
