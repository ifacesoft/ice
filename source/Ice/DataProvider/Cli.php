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
use Ice\Core\Exception;
use Ice\Exception\Error;

/**
 * Class App
 *
 * Data provider for cli streams
 *
 * @see \Ice\Core\DataProvider
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
     * @param null $key
     * @param null $default
     * @param bool $require
     * @return mixed
     * @throws Error
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.2
     * @since   0.0
     */
    public function get($key = null, $default = null, $require = false)
    {
        $this->getConnection();

        if (empty($key)) {
            return empty($_SERVER['argv']) ? [] : $_SERVER['argv'];
        }

        $value = isset($_SERVER['argv']) && array_key_exists($key, $_SERVER['argv'])
            ? $_SERVER['argv'][$key]
            : $default;

        if ($value === null && $require) {
            throw new Error(['Param {$0} from data provider {$1} is require', ['key', __CLASS__]]);
        }

        return $value;
    }

    /**
     * Set data to data provider
     *
     * @param array|null $values
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

        $this->getConnection();

        foreach ($values as $key => $value) {
            $_SERVER['argv'][$key] = $value;
        }

        return $values;
    }

    /**
     * Delete from data provider by key
     *
     * @param string $key
     * @param bool $force if true return boolean else deleted value
     * @return mixed|boolean
     *
     * @throws \Exception
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
     * @param int $step
     * @return mixed new value
     *
     * @throws \Exception
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
     * @param int $step
     * @return mixed new value
     *
     * @throws \Exception
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
     * @param string $pattern
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

        if (empty($_SERVER['argv'])) {
            return (bool)$connection;
        }

        array_shift($_SERVER['argv']);

        foreach ($_SERVER['argv'] as $key => $arg) {
            $pos = mb_strpos($arg, '=');

            if (!isset($connection['actionClass'])) {
                if ($pos === false) {
                    $connection['actionClass'] = $arg;
                } else {
                    $connection['actionClass'] = mb_substr($arg, $pos + 1);
                }

                unset($_SERVER['argv'][$key]);
                continue;
            }

            if ($pos === false) {
                throw new Error(['Cli param {$0} invalid', $key], $_SERVER['argv']);
            }
            
            $connection[mb_substr($arg, 0, $pos)] = mb_substr($arg, $pos + 1);

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

    /**
     * Set expire time (seconds)
     *
     * @param  $key
     * @param int $ttl
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
