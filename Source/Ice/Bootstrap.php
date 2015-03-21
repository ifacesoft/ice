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
use Ice\Core\Loader;
use Ice\Core\Logger;
use Ice\Core\Module;
use Ice\Core\Profiler;
use Ice\Core\Request;
use Ice\Core\Session;

/**
 * Class Bootstrap
 *
 * Initialization required components for Ice application
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
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
        $startTime = Profiler::getMicrotime();

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

        Profiler::setTiming(__CLASS__, $startTime);
    }
}

if (!defined('ICE_BOOTSTRAP')) {
    define('ICE_BOOTSTRAP', true);

    define('MODULE_DIR', php_sapi_name() == 'cli' ? getcwd() . '/' : dirname(dirname($_SERVER['PHP_SELF'])));
    define('MODULE_CONFIG_PATH', 'Config/Ice/Core/Module.php');
    define('VENDOR', basename(dirname(MODULE_DIR)) . '/' . basename(MODULE_DIR));
    define('VENDOR_DIR', strstr(MODULE_DIR, VENDOR, true));

    $autoloadPath = VENDOR_DIR . 'composer/' . VENDOR . '/autoload_real.php';

    if (file_exists($autoloadPath)) {
        require_once $autoloadPath;
        $classNames = array();
        $tokens = token_get_all(file_get_contents($autoloadPath));
        $count = count($tokens);
        for ($i = 2; $i < $count; $i++) {
            if ($tokens[$i - 2][0] == T_CLASS
                && $tokens[$i - 1][0] == T_WHITESPACE
                && $tokens[$i][0] == T_STRING
            ) {

                $class_name = $tokens[$i][1];
                $classNames[] = $class_name;
            }
        }
        $autoloadClass = reset($classNames);
        $loader = $autoloadClass::getLoader();
    } else {
        $loader = require VENDOR_DIR . 'autoload.php';
    }

    Bootstrap::init($loader);
}