<?php
/**
 * Ice core data provider abstract class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

use Ice\Core;
use Ice\Helper\Object;

/**
 * Class DataProvider
 *
 * Core data provider abstract class
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Core
 */
abstract class DataProvider
{
    use Core;

    const PREFIX_KEY_DELIMETER = '/_';
    const DEFAULT_INDEX = 'default';

    /**
     * Stored data providers
     *
     * @var DataProvider[]
     */
    private static $_dataProviders = [];
    protected $options = [];
    /**
     * Connection of data provider
     *
     * @var mixed
     */
    private $connection = null;
    /**
     * Data provider key
     *
     * @var string
     */
    private $key = null;
    /**
     * Data provider index
     *
     * @var string
     */
    private $index = null;
    /**
     * Data provider scheme
     *
     * @var string
     */
    private $scheme = null;

    private $keyPrefix = null;

    /**
     * Constructor of Data provider
     *
     * @param $key
     * @param $index
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    protected function __construct($key, $index)
    {
        $this->key = $key;
        $this->index = $index;

        if ($this->options !== null && $key != Config::getClass() && $key != Environment::getClass()) {
            $dataProviderKey = __CLASS__ . '/' . get_class($this) . '/' . $key;

            if (Environment::isLoaded()) {
                foreach (Environment::getInstance()->gets($dataProviderKey, []) as $key => $value) {
                    $this->options[$key] = is_array($value) ? reset($value) : $value;
                }
            }
        }
    }

    /**
     * Return new instance of data provider
     *
     * @param  $key
     * @param  string $index
     * @return DataProvider
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public static function getInstance($key = null, $index = DataProvider::DEFAULT_INDEX)
    {
        /**
         * @var DataProvider $class
         */
        $class = self::getClass();

        if (!$key && $class == __CLASS__) {
            Logger::getInstance(__CLASS__)->exception(
                'Not known how create instance of data provider. Need data provider key.',
                __FILE__,
                __LINE__
            );
        }

        if (!$key) {
            return DataProvider::getInstance($class, $index);
        }

        if ($class == __CLASS__) {
            if ($pos = strpos($key, '/')) {
                $class = Object::getClass(__CLASS__, substr($key, 0, $pos));
                $key = substr($key, $pos + 1);
            } else {
                $class = Object::getClass(__CLASS__, $key);
                $key = $class::getDefaultKey();
            }

            return $class::getInstance($key, $index);
        }

        if ($key == 'default') {
            $key = $class::getDefaultKey();
        }

        /**
         * @var string $class
         */
        if (isset(self::$_dataProviders[$class][$key][$index])) {
            return self::$_dataProviders[$class][$key][$index];
        }

        return self::$_dataProviders[$class][$key][$index] = new $class($key, $index);
    }

    /**
     * Return default key
     *
     * @return string
     * @throws Exception
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since   0.5
     */
    protected static function getDefaultKey()
    {
        return Logger::getInstance(__CLASS__)->exception(
            ['Need implements {$0} for {$1}', [__METHOD__, self::getClass()]],
            __FILE__,
            __LINE__
        );
    }

    /**
     * Return current scheme
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * Set new data provider scheme
     *
     * @param string $scheme
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function setScheme($scheme)
    {
        $this->scheme = $scheme;
    }

    /**
     * Get instance connection of data provider
     *
     * @throws Exception
     * @return mixed
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function getConnection()
    {
        if ($this->connection !== null) {
            return $this->connection;
        }

        if (!$this->connect($this->connection)) {
            Logger::getInstance(__CLASS__)->exception(
                [
                    'Data provider "{$0}" connection failed',
                    get_class($this) . '/' . $this->getKey() . ' (index: ' . $this->getIndex() . ')'],
                __FILE__,
                __LINE__
            );
        }

        return $this->connection;
    }

    /**
     * Connect to data provider
     *
     * @param  $connection
     * @return boolean
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    abstract protected function connect(&$connection);

    /**
     * Get current data provider key
     *
     * @return string
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Get current data provider index
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * Close self connection
     *
     * @return boolean
     * @throws Exception
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function closeConnection()
    {
        if (!$this->close($this->connection)) {
            Logger::getInstance(__CLASS__)->exception(
                [
                    'Не удалось закрыть соединенеие с дата провайдером {$0}',
                    get_class($this) . '/' . $this->getKey() . ' (index: ' . $this->getIndex() . ')'
                ],
                __FILE__,
                __LINE__
            );
        }

        $this->connection = null;
        return true;
    }

    /**
     * Close connection with data provider
     *
     * @param $connection
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    abstract protected function close(&$connection);

    /**
     * Get data from data provider by key
     *
     * @param  string $key
     * @param null $default
     * @param bool $require
     * @return mixed
     * @author anonymous <email>
     *
     * @version 1.2
     * @since   0.0
     */
    abstract public function get($key = null, $default = null, $require = false);

    /**
     * Set data to data provider
     *
     * @param array $values
     * @param  null $ttl
     * @return mixed setted value
     *

     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    abstract public function set(array $values = null, $ttl = null);

    /**
     * Delete from data provider by key
     *
     * @param  string $key
     * @param  bool $force if true return boolean else deleted value
     * @throws Exception
     * @return mixed|boolean
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    abstract public function delete($key, $force = true);

    /**
     * Increment value by key with defined step (default 1)
     *
     * @param  $key
     * @param  int $step
     * @return mixed new value
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    abstract public function incr($key, $step = 1);

    /**
     * Decrement value by key with defined step (default 1)
     *
     * @param  $key
     * @param  int $step
     * @return mixed new value
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    abstract public function decr($key, $step = 1);

    /**
     * Flush all stored data
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    abstract public function flushAll();

    /**
     * Return keys by pattern
     *
     * @param  string $pattern
     * @return array
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    abstract public function getKeys($pattern = null);

    /**
     * Return full key
     *
     * @param  $key
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function getFullKey($key)
    {
        return $this->getKeyPrefix() . DataProvider::PREFIX_KEY_DELIMETER . $key;
    }

    /**
     * Return prefix of key
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    protected function getKeyPrefix()
    {
        if ($this->keyPrefix !== null) {
            return $this->keyPrefix;
        }

        /** @var DataProvider $class */
        $class = get_class($this);

        return $this->keyPrefix = str_replace(
            '\\',
            '/',
            DataProvider::getModuleAlias() . '/' .
            $class::getClassName() . '/' .
            $this->getKey() . '/' .
            $this->getIndex()
        );
    }

    /**
     * Return connection options
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since   0.0
     */
    protected function getOptions()
    {
        return $this->options;
    }
}
