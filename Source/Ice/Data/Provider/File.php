<?php
/**
 * Ice data provider implementation file class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Data\Provider;

use Ice\Core\Data_Provider;
use Ice\Core\Debuger;
use Ice\Core\Exception;
use Ice\Core\Loader;
use Ice\Core\Module;
use Ice\Helper\Directory;
use Ice\Helper\File as Helper_File;
use Ice\Helper\Hash;

/**
 * Class Apc
 *
 * Data provider for file date storage
 *
 * @see Ice\Core\Data_Provider
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Data_Provider
 */
class File extends Data_Provider
{
    const DEFAULT_DATA_PROVIDER_KEY = 'Ice:File/default';
    const DEFAULT_KEY = 'instance';

    /**
     * Return default data provider key
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
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
     * @version 0.0
     * @since   0.0
     */
    public function delete($key, $force = true)
    {
        $fileName = $this->getFileName($key);

        if (file_exists($fileName)) {
            if ($force) {
                return unlink($fileName);
            }

            $value = $this->get($key);

            unlink($fileName);

            return $value;
        }

        return false;
    }

    /**
     * Get storage file name
     *
     * @param  $key
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    private function getFileName($key)
    {
        return $this->getConnection() . $this->getFullKey($key) . '.php';
    }

    /**
     * Get data from data provider by key
     *
     * @param  string $key
     * @return mixed
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function get($key = null)
    {
        $fileName = $this->getFileName($key);

        if (!file_exists($fileName)) {
            return null;
        }

        list($ttl, $hash, $value) = Helper_File::loadData($fileName);

        if (time() - filemtime($fileName) > $ttl) {
            return null;
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
        return $this->set($key, $this->get($key) + $step);
    }

    /**
     * Set data to data provider
     *
     * @param  string $key
     * @param  $value
     * @param  null $ttl
     * @return mixed setted value
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
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

        if ($ttl == -1) {
            return $value;
        }

        if ($ttl === null) {
            $options = $this->getOptions();
            $ttl = isset($options['ttl']) ? $options['ttl'] : 3600;
        }

        Helper_File::createData(
            $this->getFileName($key),
            [$ttl, Hash::get($value, Hash::HASH_CRC32), $value],
            true,
            0,
            false
        );

        return $value;
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
     * @version 0.0
     * @since   0.0
     */
    public function flushAll()
    {
        Directory::get(Directory::remove($this->getConnection()));
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

        if (!isset($options['path'])) {
            $options['path'] = Module::getInstance()->get('cacheDir');
        }

        $connection = $options['path'];
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
        $connection = null;
        return true;
    }
}
