<?php
/**
 * Ice core environment class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

use Ice\Core;
use Ice\Exception\Access_Denied_Environment;

/**
 * Class Environment
 *
 * Core environment class
 *
 * @see Ice\Core\Container
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Core
 */
class Environment extends Config
{
    const PRODUCTION = 'production';
    const TEST = 'test';
    const DEVELOPMENT = 'development';

    private static $instance = null;

    /**
     * Return application environment
     *
     * @param  string $environmentName
     * @param  null $postfix
     * @param  bool $isRequired
     * @param  null $ttl
     * @return Environment
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since   0.5
     */
    public static function getInstance(
        $environmentName = Environment::PRODUCTION,
        $postfix = null,
        $isRequired = false,
        $ttl = null
    )
    {
        if (Environment::$instance !== null) {
            return Environment::$instance;
        }

        $host = Request::host();

        $config = Config::getInstance(__CLASS__, null, true);

        foreach ($config->gets('environments') as $hostPattern => $name) {
            $matches = [];
            preg_match($hostPattern, $host, $matches);

            if (!empty($matches)) {
                $environmentName = is_array($name) ? reset($name) : $name;
                break;
            }
        }

        $environment = [];

        foreach ($config->gets() as $name => $env) {
            if ($name == 'environments') {
                continue;
            }

            $environment = array_merge_recursive($env, $environment);
            if ($name == $environmentName) {
                break;
            }
        }

        return Environment::$instance = Environment::create($environmentName, $environment);
    }

    public static function isLoaded()
    {
        return Environment::$instance;
    }

    public static function checkAccess($environments, $message)
    {
        if (!$environments || in_array(Environment::getInstance()->getName(), (array) $environments)) {
            return;
        }

        throw new Access_Denied_Environment($message);
    }

    /**
     * Check to current environment is development
     *
     * @return bool
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function isDevelopment()
    {
        return $this->getName() == Environment::DEVELOPMENT;
    }

    /**
     * Check to current environment is development
     *
     * @return bool
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function isProduction()
    {
        return $this->getName() == Environment::PRODUCTION;
    }

    /**
     * Retern Data Provider by class name
     *
     * @param  string $class Class (found data provider for this class)
     * @param  string $index Index of data provider
     * @return Data_Provider
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function getProvider($class, $index)
    {
        return Data_Provider::getInstance($this->getDataProviderKey($class, $index), $index);
    }

    /**
     * Return data provider key by class name
     *
     * @param  $class
     * @param  $index
     * @throws Exception
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function getDataProviderKey($class, $index)
    {
        $key = 'dataProviderKeys/' . $class . '/' . $index;

        $dataProviderKey = $this->get($key);

        if ($dataProviderKey === null) {
            Environment::getLogger()->exception(
                ['In environment config param {$0} not found', $key],
                __FILE__,
                __LINE__
            );
        }

        return is_array($dataProviderKey)
            ? reset($dataProviderKey)
            : $dataProviderKey;
    }
}
