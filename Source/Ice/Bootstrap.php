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
     * @version 0.0
     * @since 0.0
     */
    public static function init()
    {
        $startTime = microtime(true);

        define('ICE_DIR', dirname(dirname(__DIR__)) . '/');

        define('ROOT_DIR', dirname(ICE_DIR) . '/');

        define('ICE_SOURCE_DIR', ICE_DIR . 'Source/');

        $moduleName = basename(MODULE_DIR);

        define('CACHE_DIR', ROOT_DIR . '_cache/' . $moduleName . '/');
        define('LOG_DIR', ROOT_DIR . '_log/' . $moduleName . '/');
        define('RESOURCE_DIR', ROOT_DIR . '_resource/' . $moduleName . '/resource/');
        define('STORAGE_DIR', ROOT_DIR . '_storage/' . $moduleName . '/');
        define('VENDOR_DIR', ROOT_DIR . '_vendor/');

        include_once ICE_SOURCE_DIR . 'Ice/Core.php';
        include_once ICE_SOURCE_DIR . 'Ice/Core/Container.php';
        include_once ICE_SOURCE_DIR . 'Ice/Core/Loader.php';
        include_once ICE_SOURCE_DIR . 'Ice/Helper/Object.php';
        include_once ICE_SOURCE_DIR . 'Ice/Core/Environment.php';
        include_once ICE_SOURCE_DIR . 'Ice/Core/Request.php';
        include_once ICE_SOURCE_DIR . 'Ice/Core/Config.php';
        include_once ICE_SOURCE_DIR . 'Ice/Core/Data/Provider.php';
        include_once ICE_SOURCE_DIR . 'Ice/Data/Provider/Object.php';
        include_once ICE_SOURCE_DIR . 'Ice/Data/Provider/Registry.php';
        include_once ICE_SOURCE_DIR . 'Ice/Core/Module.php';
        include_once ICE_SOURCE_DIR . 'Ice/Helper/File.php';
        include_once ICE_SOURCE_DIR . 'Ice/Core/Logger.php';
        include_once ICE_SOURCE_DIR . 'Ice/Helper/Console.php';
        include_once ICE_SOURCE_DIR . 'Ice/Core/Exception.php';
        include_once ICE_SOURCE_DIR . 'Ice/Core/Response.php';
        include_once ICE_SOURCE_DIR . 'Ice/Helper/Php.php';
        include_once ICE_SOURCE_DIR . 'Ice/Helper/Directory.php';

        try {
            setlocale(LC_ALL, 'en_US.UTF-8');
            setlocale(LC_NUMERIC, 'C');

            date_default_timezone_set('UTC');

            Logger::init();

            Loader::register('Ice\Core\Loader::load');


//            include_once VENDOR_DIR . 'autoload.php';

            if (!Request::isCli()) {
                Session::init();
            }
        } catch (\Exception $e) {
            die('Bootstraping failed: ' . $e->getMessage());
        }

        if (!Environment::isProduction()) {
            if (function_exists('fb') && !headers_sent()) {
                fb('bootstrapping time: ' . Logger::microtimeResult($startTime) * 1000 . ' ms | ' . Memory::memoryGetUsagePeak(), 'INFO');
            }
        }
    }
}