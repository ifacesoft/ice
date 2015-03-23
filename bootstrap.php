<?php
if (!defined('ICE_BOOTSTRAP')) {
    define('ICE_BOOTSTRAP', true);

    if (!defined('VENDOR_DIR')) {
        $vendorDir = realpath(dirname(__DIR__) . '/_vendor');

        if (!$vendorDir) {
            die('Vendor dir not found. Please, run \'php composer.phar update --prefer-source\'' . "\n");
        }

        define('VENDOR_DIR', $vendorDir . '/');
    }

    if (!defined('MODULE_DIR')) {
        $moduleDir = php_sapi_name() == 'cli'
            ? getcwd()
            : dirname(dirname($_SERVER['SCRIPT_FILENAME']));

        define('MODULE_DIR', $moduleDir . '/');
    }

    if (!defined('ICE_DIR')) {
        define('ICE_DIR', __DIR__ . '/');
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

    require_once ICE_DIR . 'Source/Ice/Core/Data/Provider.php';
    require_once ICE_DIR . 'Source/Ice/Core/View/Render.php';
    require_once ICE_DIR . 'Source/Ice/Helper/Api/Client/Yandex/Translate.php';

    var_dump(\Ice\Core\Bootstrap::getInstance(BOOTSTRAP_CLASS));

    \Ice\Core\Bootstrap::getInstance(BOOTSTRAP_CLASS)->init($loader);
}