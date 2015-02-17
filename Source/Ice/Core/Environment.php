<?php
/**
 * Ice core environment class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

use Ice\Core;
use Ice\Data\Provider\Repository;
use Ice\Helper\Config as Helper_Config;

/**
 * Class Environment
 *
 * Core environment class
 *
 * @see Ice\Core\Container
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Core
 */
class Environment
{
    use Core;

    const PRODUCTION = 'production';
    const TEST = 'test';
    const DEVELOPMENT = 'development';

    /**
     * Environment config params
     *
     * @var array
     */
    private $_environment = [];

    /**
     * Environment name (production|test|development)
     *
     * @var string
     */
    private $_name = null;

    /**
     * Protected constructor of environment object
     *
     * @param $environmentName string Name of environment (production|test|development)
     *
     * @param array $environment
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.0
     */
    private function __construct($environmentName, $environment = [])
    {
        $this->_name = $environmentName;

        if (!empty($environment)) {
            $this->_environment = $environment;
        }

        foreach (Environment::getConfig()->gets() as $name => $environment) {
            if ($name == 'environments') {
                continue;
            }

            $this->_environment = array_merge_recursive($environment, $this->_environment);
            if ($name == $environmentName) {
                break;
            }
        }
    }

    /**
     * Get instance of environment
     *
     * @param null $key
     * @return Environment
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.0
     */
    public static function getInstance($key = null)
    {
        if (!$key) {
            $key = Environment::getDefaultKey();
        }

        /** @var Repository $dataProvider */
        $dataProvider = Environment::getRepository();

        if ($_config = $dataProvider->get($key)) {
            return $_config;
        }

        return $dataProvider->set($key, Environment::create($key));
    }

    /**
     * Check to current environment is development
     *
     * @return bool
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public static function isDevelopment()
    {
        return self::getInstance()->getName() == self::DEVELOPMENT;
    }

    /**
     * Check to current environment is development
     *
     * @return bool
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public static function isProduction()
    {
        return Environment::getInstance()->getName() == self::PRODUCTION;
    }

    /**
     * Return default key of data provider
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    protected static function getDefaultKey()
    {
        $host = Request::host();

        foreach (Environment::getConfig()->gets('environments') as $hostPattern => $environment) {
            $matches = [];
            preg_match($hostPattern, $host, $matches);

            if (!empty($matches)) {
                return $environment;
            }
        }

        return self::PRODUCTION;
    }

    /**
     * Create new instance of environment
     *
     * @param string $environment Name of environment (production|test|devlepment)
     *
     * @return Environment
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public static function create($environment)
    {
        return new Environment($environment);
    }

    /**
     * Retern Data Provider by class name
     *
     * @param string $class Class (found data provider for this class)
     * @param string $index Index of data provider
     * @return Data_Provider
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getProvider($class, $index)
    {
        return Data_Provider::getInstance($this->getDataProviderKey($class, $index), $index);
    }

    /**
     * Return data provider key by class name
     *
     * @param $class
     * @param  $index
     * @throws Exception
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getDataProviderKey($class, $index)
    {
        $key = 'dataProviderKeys/' . $class . '/' . $index;

        $dataProviderKey = $this->get($key);

        if ($dataProviderKey === null) {
            Environment::getLogger()->exception(['In environment config param {$0} not found', $key], __FILE__, __LINE__);
        }

        return is_array($dataProviderKey)
            ? reset($dataProviderKey)
            : $dataProviderKey;
    }

    /**
     * Environment config param value (one (first) value)
     *
     * @param string|null $key
     * @param bool $isRequired
     * @throws Exception
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.0
     */
    public function get($key = null, $isRequired = true)
    {
        $params = $this->gets($key, $isRequired);

        return empty($params) ? null : reset($params);
    }

    /**
     * Environment config param values
     *
     * @param string|null $key
     * @param bool $isRequired
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.0
     */
    public function gets($key = null, $isRequired = true)
    {
        return Helper_Config::gets($this->_environment, $key, $isRequired);
    }

    /**
     * Return current environment name
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Restore object
     *
     * @param array $data
     * @return object
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.5
     */
    public static function __set_state(array $data)
    {
        return new self($data['_environmentName'], $data['_environment']);
    }
}