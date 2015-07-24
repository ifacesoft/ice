<?php
if (defined('ICE_BOOTSTRAP')) {
    return;
}

define('ICE_BOOTSTRAP', true);

if (!defined('ICE_DIR')) {
    define('ICE_DIR', __DIR__ . '/');
}

if (!defined('MODULE_DIR')) {
    $moduleDir = php_sapi_name() == 'cli'
//            ? dirname(realpath($_SERVER['argv'][0]))
        ? getcwd()
        : dirname(dirname($_SERVER['SCRIPT_FILENAME']));

    if (file_exists($moduleDir . '/app.php')) {
        define('MODULE_DIR', $moduleDir . '/');
    } else {
        define('MODULE_DIR', ICE_DIR);
    }
}

if (!defined('VENDOR_DIR')) {
    define('VENDOR_DIR', MODULE_DIR . 'Var/vendor/');
}

if (!defined('BOOTSTRAP_CLASS')) {
    define('BOOTSTRAP_CLASS', 'Ice:Ice');
}

if (!defined('MODULE_CONFIG_PATH')) {
    define('MODULE_CONFIG_PATH', 'Config/Ice/Core/Module.php');
}

global $loader;

if (!$loader) {
    $loader = require VENDOR_DIR . 'autoload.php';
}

$startTime = \Ice\Core\Profiler::getMicrotime();
$startMemory = \Ice\Core\Profiler::getMemoryGetUsage();

require_once ICE_DIR . 'Source/Ice/Core/Data/Provider.php';
require_once ICE_DIR . 'Source/Ice/Data/Provider/File.php';
require_once ICE_DIR . 'Source/Ice/Data/Provider/Apc.php';
require_once ICE_DIR . 'Source/Ice/Data/Provider/Redis.php';
require_once ICE_DIR . 'Source/Ice/Core/View/Render.php';
require_once ICE_DIR . 'Source/Ice/Helper/Api/Client/Yandex/Translate.php';

try {
    \Ice\Core\Bootstrap::getInstance(BOOTSTRAP_CLASS)->init($loader);

    \Ice\Core\Profiler::setPoint(BOOTSTRAP_CLASS, $startTime, $startMemory);
} catch (Exception $e) {
    echo '<span style="font-weight: bold;">Bootstrapping failed: ' .
        str_replace(MODULE_DIR, '', $e->getMessage()) .
        '</span><br>';
    echo nl2br(str_replace(MODULE_DIR, '', $e->getTraceAsString()));
    die('Terminated. Bye-bye...' . "\n");
}