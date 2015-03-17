<?php
/**
 * Ice bootstrap class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice;

use Composer\Autoload\ClassLoader;
use Ice;
use Ice\Core\Environment;
use Ice\Core\Loader;
use Ice\Core\Logger;
use Ice\Core\Module;
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
     * @param ClassLoader $loader
     * @param bool $force
     */
    public static function init(ClassLoader $loader, $force = false)
    {
        $startTime = microtime(true);

        setlocale(LC_ALL, 'en_US.UTF-8');
        setlocale(LC_NUMERIC, 'C');

        date_default_timezone_set('UTC');

        try {
            require_once Module::getInstance('Ice')->get('sourceDir') . 'Ice/Core/Data/Provider.php';
            require_once Module::getInstance('Ice')->get('sourceDir') . 'Ice/Core/View/Render.php';

            Loader::init($loader, $force);
            Logger::init();
            Request::init();

            if (Request::isOptions()) {
                exit;
            }

            if (!Request::isCli()) {
                Session::init();
            }
        } catch (\Exception $e) {
            echo '<span style="background-color: red; color:white; font-weight: bold;">Bootstrapping failed: ' . $e->getMessage() . '</span><br>';
            echo nl2br($e->getTraceAsString());
            die('Terminated. Bye-bye...' . "\n");
        }

        if (!Environment::getInstance()->isProduction()) {
            Logger::fb('bootstrapping time: ' . Logger::microtimeResult($startTime) . ' | ' . Memory::memoryGetUsagePeak(), 'INFO');
        }
    }
}