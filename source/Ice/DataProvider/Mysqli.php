<?php
/**
 * Ice data provider implementation mysqli class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\DataProvider;

use Ice\Core\DataProvider;
use Ice\Core\Debuger;
use Ice\Core\Exception;
use Ice\Core\Logger;
use Ice\Exception\DataSource;
use Ice\Exception\Error;
use Ice\Helper\Date;
use Symfony\Component\Debug\Debug;

/**
 * Class Mysqli
 *
 * Data provider for Mysql connection
 *
 * @see \Ice\Core\DataProvider
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage DataProvider
 */
class Mysqli extends DataProvider
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
     * @param null $default
     * @param bool $require
     * @return array|null
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.2
     * @since   0.0
     */
    public function get($key = null, $default = null, $require = false)
    {
        // TODO: Implement getKeys() method.
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
        if ($ttl === -1) {
            return $values;
        }

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
            throw new DataSource(['mysql - #' . $this->getConnection()->errno . ': {$0}', $this->getConnection()->error]);
        }

        parent::setScheme($scheme);
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
     * @throws Exception
     */
    public function getConnection()
    {
        return parent::getConnection();
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
     * @version 1.13
     * @since   0.0
     * @throws Exception
     */
    protected function connect(&$connection)
    {
        $options = $this->getOptions();
        
        list($user, $pass, $host, $port, $charset) = $options->getParams(['username', 'password', 'host', 'port', 'charset']);

        $connection = \mysqli_init();

        try {
            $connect = $connection->real_connect(/*'p:' . */$host, $user, $pass, null, $port);

            $error = $connect ? $connection->connect_error : 'Connect mysql failed';

            if ($error) {
                $connection = null;

                throw new DataSource($error . ' (' . $options->getName() . ')');
            }
        } catch (\Exception $e) {
            $connection = null;

            return false;
        } catch (\Throwable $e) {
            $connection = null;

            return false;
        }

        $connection->set_charset($charset);

//        ALTER DATABASE dreams_twitter CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci;
        
        // todo: параметры соединения должны назначиться в конфиге
        $connection->query('SET SESSION time_zone = "' . Date::getServerTimezone() . '";');
        $connection->query('SET SESSION group_concat_max_len = 4294967295;');
        $connection->query('SET NAMES utf8mb4;');

        return (bool)$connection;
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

    /**
     * Set expire time (seconds)
     *
     * @param  $key
     * @param  int $ttl
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

    /**
     * @param null $key
     * @param string $index
     * @return Mysqli
     * @throws Exception
     */
    public static function getInstance($key = null, $index = DataProvider::DEFAULT_INDEX)
    {
        return parent::getInstance($key, $index);
    }
}
