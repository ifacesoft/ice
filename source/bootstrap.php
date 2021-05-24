<?php

use Ice\Core\Environment;
use Ice\Core\Loader;
use Ice\Core\Logger;
use Ice\Core\Module;

ob_start();
ob_implicit_flush(false);

define('ICE_VENDOR_NAME', 'ifacesoft/ice');
define('ICE_CONFIG_PATH', 'config/Ice/Core/Module.php');
define('ICE_RUN_PATH', 'source/run.php');
define('ICE_BOOTSTRAP_PATH', 'source/bootstrap.php');

if(!defined('STDIN'))  define('STDIN',  fopen('php://stdin',  'rb'));
if(!defined('STDOUT')) define('STDOUT', fopen('php://stdout', 'wb'));
if(!defined('STDERR')) define('STDERR', fopen('php://stderr', 'wb'));

$vendorRealPath = dirname(dirname(dirname(dirname(__DIR__)))) . '/vendor';

if (!$vendorRealPath) {
    $vendorRealPath = dirname(__DIR__) . '/vendor';

    if (!$vendorRealPath) {
        throw new \Error('Project constants not defined');
    }
}

if (!defined('VENDOR_DIR')) {
    define('VENDOR_DIR', $vendorRealPath . '/');
}

if (!defined('MODULE_DIR')) {
    define('MODULE_DIR', dirname(VENDOR_DIR) . '/');
}

if (is_file(MODULE_DIR . 'config.ice')) {
    define('MODULE_CONFIG_PATH', file_get_contents(MODULE_DIR . 'config.ice'));
} else {
    define('MODULE_CONFIG_PATH', ICE_CONFIG_PATH);
}

if (!defined('NODE_MODULES_DIR')) {
    define('NODE_MODULES_DIR', MODULE_DIR . 'node_modules/');
}

if ($iceRealPath = realpath(VENDOR_DIR . ICE_VENDOR_NAME)) {
    define('ICE_DIR', $iceRealPath . '/');
} else {
    define('ICE_DIR', MODULE_DIR);
}

try {
    global $loader;

    if (empty($loader)) {
        $loader = require VENDOR_DIR . 'autoload.php';
    }

    require_once ICE_DIR . 'source/Ice/Helper/Class/Object.php';

    Module::init();
    Environment::getInstance();
    Loader::init();
    Logger::init();

    setlocale(LC_ALL, 'en_US.UTF-8');
    setlocale(LC_NUMERIC, 'C');

    date_default_timezone_set(getServerTimeZone());

    set_error_handler('Ice\Core\Logger::errorHandler');
    register_shutdown_function('Ice\Core\Logger::shutdownHandler');
} catch (Exception $e) {
    echo '<span style="font-weight: bold;">Bootstrapping failed: ' .
        str_replace(MODULE_DIR, '', $e->getMessage()) .
        '</span><br>';
    //echo nl2br(str_replace(MODULE_DIR, '', $e->getTraceAsString()) . "\n");
    die('Terminated. Bye-bye...' . "\n");
}
