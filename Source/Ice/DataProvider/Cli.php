<?php
/**
 * Ice data provider implementation cli class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\DataProvider;

use Ice\Core\Action;
use Ice\Core\DataProvider;

/**
 * Class Cli
 *
 * Data provider for cli streams
 *
 * @see Ice\Core\DataProvider
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage DataProvider
 */
class Cli extends DataProvider
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
     * Get data from data provider by key
     *
     * @param  string $key
     * @return mixed
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since   0.0
     */
    public function get($key = null)
    {
        $this->getConnection();

        if (!$key) {
            return $_SERVER['argv'];
        }

        return isset($_SERVER['argv'][$key]) ? $_SERVER['argv'][$key] : null;
    }

    /**
     * Set data to data provider
     *
     * @param  string $key
     * @param  $value
     * @param  null $ttl
     * @throws \Exception
     * @return mixed setted value
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
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

        $this->getConnection();

        return $_SERVER['argv'][$key] = $value;
    }

    /**
     * Delete from data provider by key
     *
     * @param  string $key
     * @param  bool $force if true return boolean else deleted value
     * @throws \Exception
     * @return mixed|boolean
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function delete($key, $force = true)
    {
        throw new \Exception('Implement delete() method.');
    }

    /**
     * Increment value by key with defined step (default 1)
     *
     * @param  $key
     * @param  int $step
     * @throws \Exception
     * @return mixed new value
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function incr($key, $step = 1)
    {
        throw new \Exception('Implement inc() method.');
    }

    /**
     * Decrement value by key with defined step (default 1)
     *
     * @param  $key
     * @param  int $step
     * @throws \Exception
     * @return mixed new value
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function decr($key, $step = 1)
    {
        throw new \Exception('Implement dec() method.');
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
        throw new \Exception('Implement flushAll() method.');
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
        // TODO: Implement getKeys() method.
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
        $connection = [];

        array_shift($_SERVER['argv']);

        foreach ($_SERVER['argv'] as $key => $arg) {
            $param = explode('=', $arg);

            if (!isset($connection['actionClass'])) {
                if (count($param) == 1) {
                    $connection['actionClass'] = Action::getClass($arg);
                    unset($_SERVER['argv'][$key]);
                    continue;
                }

                if (count($param) == 2) {
                    list($param, $value) = $param;

                    if ($param == 'action') {
                        $connection['actionClass'] = Action::getClass($value);
                        unset($_SERVER['argv'][$key]);
                        continue;
                    }
                }
            }

            if (count($param) == 2) {
                list($param, $value) = $param;
            } else {
                $param = $arg;
                $value = null;
            }

            $connection[$param] = $value;

            unset($_SERVER['argv'][$key]);
        }

        $_SERVER['argv'] = $connection;

        return $connection;
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
