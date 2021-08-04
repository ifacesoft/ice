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
use Ice\DataProvider\Registry;
use Ice\DataProvider\Repository;
use Ice\Exception\Config_Error;
use Ice\Exception\Config_Param_NotFound;
use Ice\Exception\Error;
use Ice\Exception\FileNotFound;
use Ice\Helper\Class_Object;
use Ice\Helper\Config as Helper_Config;
use Ice\Helper\File;
use Ifacesoft\Ice\Core\Domain\Value\StringValue;

/**
 * Class Config
 *
 * Core config class
 *
 * @see \Ice\Core\Container
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @todo 1. методы сделать не статичными. 2. Сделвть итерацию по конфигу. 3. удалить метод gets
 *
 * @package    Ice
 * @subpackage Core
 */
class Config
{
    use Stored;

    private static $cacheData = [];

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
     * Return default config for class
     *
     * @param  $key
     * @return Config
     *
     * @throws Config_Error
     * @throws Exception
     * @throws FileNotFound
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since   0.5
     */
    public static function getDefault($key)
    {
        return self::getInstance(__CLASS__)->getConfig('defaults/' . $key);
    }

    /**
     * @param $key
     * @return Config
     * @throws Config_Error
     * @throws FileNotFound
     */
    public function getConfig($key)
    {
        return self::create($this->getName() . '/' . $key, $this->gets($key));
    }

    /**
     * Return new Config
     *
     * @param  $configRouteName
     * @param array $configData
     * @return Config
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since   0.5
     */
    public static function create($configRouteName, array $configData)
    {
        $configClass = self::getClass();

        /** @var Config $config */
        $config = new $configClass();

        $config->name = $configRouteName;
        $config->config = $configData;

        return $config;
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
     * Get config param values
     *
     * @param string|null $key
     * @param bool $isRequired_default @todo: разделить эти аргументы
     * @return array
     * @throws Config_Error
     * @throws FileNotFound
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @deprecated 1.10 use Config::get($key, (array) $default)
     *
     * @version 1.1
     * @since   0.0
     */
    public function gets($key = null, $isRequired_default = true)
    {
        $cacheTag = $this->getName() . '/' . $key;

        if (isset(self::$cacheData[$cacheTag])) {
            return self::$cacheData[$cacheTag];
        }

        try {
            return self::$cacheData[$cacheTag] = Helper_Config::gets($this->config, $key, $isRequired_default);
        } catch (Config_Param_NotFound $e) {
            throw new Config_Error(['Param {$0} not found in config {$1}', [$key, $this->getName()]], [], $e);
        }
    }

    /**
     * Get config object by type or key
     *
     * @param mixed $class
     * @param null $postfix
     * @param bool $isRequired
     * @param integer $ttl
     * @param array $selfConfig
     * @return Config
     * @throws Config_Error
     * @throws Exception
     * @throws FileNotFound
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.3
     * @since   0.0
     */
    public static function getInstance($class, $postfix = null, $isRequired = false, $ttl = null, array $selfConfig = [])
    {
        if ($postfix) {
            $class .= '_' . $postfix;
        }

        /**
         * @var Repository $repository
         */
        $repository = $class === Environment::class
            ? Registry::getInstance()
            : self::getRepository();

        if ($_config = $ttl >= 0 ? $repository->get($class) : null) {
            return $_config;
        }

        $config = [];

        $logger = Logger::getInstance($class);

        // todo:  Это используется в админке
        $baseClass = Class_Object::getBaseClass($class);

        if ($baseClass !== $class) {
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
        //

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
                'Ice:Config_NotFound'
            );
        }

        $coreConfig = $class === __CLASS__
            ? []
            : self::getInstance(__CLASS__)->gets($class, []);

        /** @var Config $configClass */
        $configClass = self::getClass();

        return $repository->set([$class => $configClass::create($class, array_merge_recursive($config, $coreConfig, $selfConfig))], $ttl)[$class];
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
     * Set config param
     *
     * @param $key
     * @param $value
     * @param bool $force
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
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
     * @throws Config_Error
     * @throws Error
     * @throws Exception
     * @throws FileNotFound
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since   0.5
     */
    public function backup($revision)
    {
        File::move(
            Loader::getFilePath($this->getName(), '.php', Module::CONFIG_DIR, false, true),
            Loader::getFilePath($this->getName() . '/' . $revision, '.php', 'var/backup/config/', false, true)
        );

        return $this;
    }

    /**
     * Save config
     *
     * @param null $path
     * @return Config
     * @throws Exception
     * @throws FileNotFound
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

    public function getParams(array $paramNames)
    {
        static $env = 'ENV_';

        $params = [];

        $ENV = array_merge(getenv(), $_ENV);

        foreach ($paramNames as $option => $paramName) {
            $param = $this->get($paramName);

            if (!StringValue::create($param)->startsWith($env)) {
                $params[] = $param;

                continue;
            }

            $param = substr($param, strlen($env));

            $params[] = isset($ENV[$param]) ? $ENV[$param] : $param;
        }

        return $params;
    }

    /**
     * Get config param value
     *
     * @param string|null $key
     * @param bool $isRequired_default
     * @return string|array
     *
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.10
     * @since   0.0
     */
    public function get($key = null, $isRequired_default = true)
    {
        $params = $this->gets($key, $isRequired_default);

        if (is_array($isRequired_default)) {
            return $params;
        }

        return empty($params) ? null : reset($params);
    }
}
