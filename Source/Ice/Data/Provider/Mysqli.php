<?php
/**
 * Ice data provider implementation mysqli class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Data\Provider;

use Ice\Core\DataProvider;
use Ice\Core\Debuger;
use Ice\Core\Exception;
use Ice\Core\Logger;
use Ice\Core\Profiler;
use Ice\Exception\Error;
use Ice\Helper\String;

/**
 * Class Mysqli
 *
 * Data provider for Mysql connection
 *
 * @see Ice\Core\DataProvider
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage DataProvider
 */
class Mysqli extends DataProvider
{
    const DEFAULT_KEY = 'default';

    protected $options = [
        'host' => 'localhost',
        'port' => '3306',
        'charset' => 'utf8'
    ];

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
     * @return array|null
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function get($key = null)
    {
        // TODO: Implement getKeys() method.
    }

    /**
     * Get instance connection of data provider
     *
     * @return \Mysqli
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
     * @param  string $key
     * @param  $value
     * @param  null $ttl
     * @throws \Exception
     * @return mixed setted value
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function set($key, $value = null, $ttl = null)
    {
        // TODO: Implement getKeys() method.
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
    }

    /**
     * Switch to new scheme name
     *
     * @param  string $scheme
     * @throws Exception
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function setScheme($scheme)
    {
        if (!$this->getConnection()->select_db($scheme)) {
            Logger::getInstance(__CLASS__)->exception(
                ['mysql - #' . $this->getConnection()->errno . ': {$0}', $this->getConnection()->error],
                __FILE__,
                __LINE__,
                null,
                $scheme,
                0
            );
        }

        parent::setScheme($scheme);
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
        $options = $this->getOptions();

        $connection = mysqli_init();

        $isConnected = false;

        try {
            $isConnected = $connection->real_connect(
                $options['host'],
                $options['username'],
                $options['password'],
                null,
                $options['port']
            );

            if (!$isConnected) {
                throw new Error('Connect failed');
            }
        } catch (\Exception $e) {
            Logger::getInstance(__CLASS__)
                ->info(
                    [
                        'mysql - #' . $connection->errno . ': {$0}',
                        $connection->error . ' - ' . $e->getMessage() .
                        ' (' . $options['username'] . '@' . $options['host'] . ':' . $options['port'] . ')'
                    ],
                    Logger::WARNING
                );
            return $isConnected;
        }

        $connection->set_charset($options['charset']);

        return $isConnected;
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
        $connection->close();
        $connection = null;
        return true;
    }
}
