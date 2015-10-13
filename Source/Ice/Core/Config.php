<?php
/**
 * Ice core config class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

use Ice\Core;
use Ice\Data\Provider\Repository;
use Ice\Exception\FileNotFound;
use Ice\Helper\Config as Helper_Config;
use Ice\Helper\File;
use Ice\Helper\Object;

/**
 * Class Config
 *
 * Core config class
 *
 * @see Ice\Core\Container
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Core
 */
class Config
{
    use Stored;

    /**
     * Config params
     *
     * @var array
     */
    private $config = null;

    /**
     * Config Key
     *
     * @var string
     */
    private $name = null;

    /**
     * Constructor of config object
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since   0.0
     */
    private function __construct()
    {
    }

    /**
     * Get config object by type or key
     *
     * @param  mixed $class
     * @param  null $postfix
     * @param  bool $isRequired
     * @param  integer $ttl
     * @return Config
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since   0.0
     */
    public static function getInstance($class, $postfix = null, $isRequired = false, $ttl = null)
    {
        $baseClass = Object::getBaseClass($class);

        if ($postfix) {
            $class .= '_' . $postfix;
        }

        /**
         * @var Repository $repository
         */
        $repository = self::getRepository();

        if ($_config = $ttl >= 0 ? $repository->get($class) : null) {
            return $_config;
        }

        $config = [];

        if ($class != __CLASS__ && $coreConfig = Config::getInstance(__CLASS__)->gets($class, false)) {
            $config = array_merge_recursive($coreConfig, $config);
        }

        $logger = Logger::getInstance($class);

        if ($baseClass != $class) {
            $baseClassPathes = Loader::getFilePath($baseClass, '.php', Module::CONFIG_DIR, false, false, false, true);
            foreach (array_reverse($baseClassPathes) as $configFilePath) {
                $configFromFile = File::loadData($configFilePath);

                if (!is_array($configFromFile)) {
                    $logger->exception(['Не валидный файл конфиг: {$0}', $configFilePath], __FILE__, __LINE__);
                }
                if (isset($configFromFile[$class])) {
                    $config = array_merge_recursive($configFromFile[$class], $config);
                }
            }
        }

        try {
            $classPathes = Loader::getFilePath($class, '.php', Module::CONFIG_DIR, $isRequired, false, false, true);

            foreach (array_reverse($classPathes) as $configFilePath) {
                $configFromFile = File::loadData($configFilePath);
                if (!is_array($configFromFile)) {
                    $logger->exception(['Не валидный файл конфиг: {$0}', $configFilePath], __FILE__, __LINE__);
                }
                $config = array_merge_recursive($configFromFile, $config);
            }
        } catch (FileNotFound $e) {
            $logger->exception(
                ['Config for {$0} not found', $class],
                __FILE__,
                __LINE__,
                $e,
                null,
                -1,
                'Ice:ConfigNotFound'
            );
        }

        return $repository->set($class, Config::create($class, $config), $ttl);
    }

    /**
     * Get config param values
     *
     * @param  string|null $key
     * @param  bool $isRequired
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.0
     */
    public function gets($key = null, $isRequired = true)
    {
        return Helper_Config::gets($this->config, $key, $isRequired);
    }

    /**
     * Return new Config
     *
     * @param  $configRouteName
     * @param  array $configData
     * @return Config
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since   0.5
     */
    public static function create($configRouteName, array $configData = [])
    {
        $configClass = self::getClass();

        /** @var Config $config */
        $config = new $configClass();

        $config->name = $configRouteName;
        $config->config = $configData;

        return $config;
    }

    /**
     * Return default config for class
     *
     * @param  $key
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since   0.5
     */
    public static function getDefault($key)
    {
        return Config::getInstance(__CLASS__)->gets('defaults/' . $key, false);
    }

    /**
     * Return default key
     *
     * @return Core
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    protected static function getDefaultKey()
    {
        return self::getClass();
    }

    /**
     * Get config param value
     *
     * @param  string|null $key
     * @param  bool $isRequired
     * @throws Exception
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function get($key = null, $isRequired = true)
    {
        $params = $this->gets($key, $isRequired);

        return empty($params) ? null : reset($params);
    }

    /**
     * Set config param
     *
     * @param $key
     * @param $value
     * @param bool $force
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 2.0
     * @since   0.5
     */
    public function set($key, $value = null, $force = false)
    {
        if (is_array($key)) {
            foreach ($key as $param => $value) {
                Helper_Config::set($this->config, $param, $value, $force);
            }
            return;
        }

        Helper_Config::set($this->config, $key, $value, $force);
    }

    /**
     * Remove param
     *
     * @param $key
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since   0.5
     */
    public function remove($key)
    {
        Helper_Config::remove($this->config, $key);
    }

    /**
     * Backup config
     *
     * @param  $revision
     * @return Config
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since   0.5
     */
    public function backup($revision)
    {
        File::move(
            Loader::getFilePath($this->getName(), '.php', Module::CONFIG_DIR, false, true),
            Loader::getFilePath($this->getName() . '/' . $revision, '.php', 'Var/Backup/Config/', false, true)
        );

        return $this;
    }

    /**
     * Return config name
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Save config
     *
     * @param null $path
     * @return Config
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.5
     */
    public function save($path = null)
    {
        $filePath = $path
            ? MODULE_DIR . $path . str_replace('\\', '/', $this->getName()) . '.php'
            : Loader::getFilePath($this->getName(), '.php', $path ? $path : Module::CONFIG_DIR, false, true);

        File::createData($filePath, $this->config);

        return $this;
    }

    /**
     * @param $key
     * @return Config
     */
    public function getConfig($key) {
        return Config::create($this->getName() . '/' . $key, $this->gets($key));
    }
}
