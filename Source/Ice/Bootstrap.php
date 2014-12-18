<?php
/**
 * Ice bootstrap class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice;

use Ice;
use Ice\Core\Environment;
use Ice\Core\Loader;
use Ice\Core\Logger;
use Ice\Core\Request;
use Ice\Core\Session;
use Ice\Helper\Memory;

/**
 * Class Bootstrap
 *
 * Initialization required components for Ice application
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 *
 * @version 0.0
 * @since 0.0
 */
class Bootstrap
{
    /**
     * Initialization requered parameters, constants and includes core files
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.0
     */
    public static function init()
    {
        $startTime = microtime(true);

        define('ICE_DIR', dirname(dirname(__DIR__)) . '/');

        define('ROOT_DIR', dirname(ICE_DIR) . '/');

        define('ICE_SOURCE_DIR', ICE_DIR . 'Source/');
        define('ICE_RESOURCE_DIR', ICE_DIR . 'Resource/');

        $moduleName = basename(MODULE_DIR);

        define('CACHE_DIR', ROOT_DIR . '_cache/' . $moduleName . '/');
        define('LOG_DIR', ROOT_DIR . '_log/' . $moduleName . '/');
        define('RESOURCE_DIR', ROOT_DIR . '_resource/' . $moduleName . '/resource/');
        define('STORAGE_DIR', ROOT_DIR . '_storage/' . $moduleName . '/');
        define('VENDOR_DIR', ROOT_DIR . '_vendor/');

        setlocale(LC_ALL, 'en_US.UTF-8');
        setlocale(LC_NUMERIC, 'C');

        date_default_timezone_set('UTC');

        try {
            $loader = require VENDOR_DIR . 'autoload.php';

            require_once ICE_SOURCE_DIR . 'Ice/Core/Data/Provider.php';

            Loader::load('Ice\Core\Logger');

            Loader::init($loader);
            Logger::init();

            if (!Request::isCli()) {
                Session::init();
            }
        } catch (\Exception $e) {
            die('Bootstraping failed: ' . $e->getMessage());
        }

        if (!Environment::isProduction()) {
            if (!Request::isCli() && !headers_sent()) {
                fb('bootstrapping time: ' . Logger::microtimeResult($startTime) * 1000 . ' ms | ' . Memory::memoryGetUsagePeak(), 'INFO');
            }
        }
    }
}