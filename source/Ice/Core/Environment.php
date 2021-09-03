<?php
/**
 * Ice core environment class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

use Ice\Exception\Access_Denied_Environment;
use Ice\Exception\Config_Error;
use Ice\Exception\FileNotFound;

/**
 * Class Environment
 *
 * Core environment class
 *
 * @see \Ice\Core\Container
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

    public static function isLoaded()
    {
        return self::$instance !== null;
    }

    /**
     * @param $environments
     * @param $message
     * @throws Access_Denied_Environment
     * @throws Exception
     * @throws Config_Error
     * @throws FileNotFound
     */
    public static function checkAccess($environments, $message)
    {
        if (!$environments || in_array(Environment::getInstance()->getName(), (array)$environments)) {
            return;
        }

        throw new Access_Denied_Environment($message);
    }

    /**
     * Return application environment
     *
     * @param string $environmentName
     * @param null $postfix
     * @param bool $isRequired
     * @param null $ttl
     * @param array $selfConfig
     * @return Environment|Config
     * @throws Exception
     * @throws Config_Error
     * @throws FileNotFound
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since   0.5
     */
    public static function getInstance(
        $environmentName = null,
        $postfix = null,
        $isRequired = false,
        $ttl = null,
        array $selfConfig = []
    )
    {
        if (self::$instance !== null) {
            return self::$instance;
        }

        $host = Request::host();

        $config = Config::getInstance(__CLASS__, null, true);

        if ($environmentName = getenv('ICE_ENV')) {
        } else {
            foreach ($config->gets('environments') as $hostPattern => $name) {
                $matches = [];
                preg_match($hostPattern, $host, $matches);

                if (!empty($matches)) {
                    $environmentName = is_array($name) ? reset($name) : $name;
                    break;
                }
            }
        }
        
        if ($environmentName !== self::PRODUCTION && (!empty($_SERVER['argv']) || !empty($_REQUEST['iceEnv']))) {
            if (!empty($_REQUEST['iceEnv'])) {
                $environmentName = $_REQUEST['iceEnv'];
            } else {
                foreach ($_SERVER['argv'] as $arg) {
                    if (strpos($arg, 'iceEnv') !== false) {
                        $environmentName = substr($arg, 7);
                    }
                }
            }
        }

        $environment = [];

        foreach ($config->gets() as $name => $env) {
            if ($name === 'environments') {
                continue;
            }

            $environment = array_merge_recursive($env, $environment);
            if ($name === $environmentName) {
                break;
            }
        }

        if (!$environmentName) {
            throw new \RuntimeException('Host ' . $host . ' not configured in environment');
        }

        return self::$instance = self::create($environmentName, $environment);
    }

    public function getHostname()
    {
        return gethostname();
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
        return $this->getName() === self::DEVELOPMENT;
    }

    /**
     * Check to current environment is development
     *
     * @return bool
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.4
     * @since   0.0
     */
    public function isProduction()
    {
        return $this->getName() === self::PRODUCTION
//            || Type_String::endsWith(Request::host(), '.com')
//            || Type_String::endsWith(Request::host(), '.ru')
            ;
    }

    /**
     * Retern Data Provider by class name
     *
     * @param string $class Class (found data provider for this class)
     * @param $key
     * @param string $index Index of data provider
     * @return DataProvider
     * @throws Exception
     * @version 0.0
     * @since   0.0
     * @author dp <denis.a.shestakov@gmail.com>
     *
     */
    public function getProvider($class, $key, $index = 'default')
    {
        return DataProvider::getInstance($this->getDataProviderKey($class, $key), $index);
    }

    /**
     * Return data provider key by class name
     *
     * @param $class
     * @param $key
     * @return string
     * @throws Exception
     * @version 1.1
     * @since   0.0
     * @author dp <denis.a.shestakov@gmail.com>
     *
     */
    public function getDataProviderKey($class, $key)
    {
        $dataProviderKey = $this->get('dataProviderKeys/' . $class::getBaseClass() . '/' . $key);
        return $pos = strpos($dataProviderKey, '/') ? $dataProviderKey : $dataProviderKey . '/' . $class;
    }
}
