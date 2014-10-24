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
 *
 * @version stable_0
 * @since stable_0
 */
class Session extends Data_Provider
{
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

        return $_SESSION[$key] = $value;
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
     * @param string  $key
     * @return mixed
     */
    public function get($key = null)
    {
        if (empty($key)) {
            return $_SESSION;
        }

        return $_SESSION[$key];
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
        $_SESSION[$key] += $step;
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
        $_SESSION[$key] -= $step;
    }

    /**
     * Flush all stored data
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
        return isset($_SESSION);
    }

    /**
     * Close connection with data provider
     *
     * @param $connection
     * @return boolean
     */
    protected function close(&$connection)
    {
        return true;
    }
}