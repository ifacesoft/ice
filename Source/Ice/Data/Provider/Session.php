<?php
/**
 * Ice data provider implementation session class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Data\Provider;

use Ice\Core\Data_Provider;
use Ice\Core\Exception;

/**
 * Class Session
 *
 * Data provider for session data
 *
 * @see Ice\Core\Data_Provider
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Data_Provider
 */
class Session extends Data_Provider
{
    const DEFAULT_DATA_PROVIDER_KEY = 'Ice:Session/default';
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
     *
     * @version 0.0
     * @since 0.0
     */
    public function set($key, $value, $ttl = null)
    {
        return $_SESSION[$key] = $value;
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
            unset($_SESSION[$key]);
            return true;
        }

        $value = $this->get($key);

        unset($_SESSION[$key]);

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
        if (empty($key)) {
            return $_SESSION;
        }

        return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
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
     * @version 0.4
     * @since 0.0
     */
    public function incr($key, $step = 1)
    {
        return $_SESSION[$key] += $step;
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
     * @version 0.4
     * @since 0.0
     */
    public function decr($key, $step = 1)
    {
        return $_SESSION[$key] -= $step;
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
        $_SESSION = [];
    }

    /**
     * Return keys by pattern
     *
     * @param string $pattern
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @todo 0.4 implements filter by pattern
     * @version 0.4
     * @since 0.0
     */
    public function getKeys($pattern = null)
    {
        return array_keys($_SESSION);
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
        return isset($_SESSION);
    }

    /**
     * Close connection with data provider
     *
     * @param $connection
     * @return boolean
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.0
     */
    protected function close(&$connection)
    {
        unset($_SESSION);
        return true;
    }
}