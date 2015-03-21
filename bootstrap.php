<?php
use Ice\Bootstrap;

if (!defined('ICE_BOOTSTRAP')) {

    define('MODULE_DIR', __DIR__ . '/');
    define('MODULE_CONFIG_PATH', 'Config/Ice/Core/Module.php');
    define('VENDOR_DIR', dirname(MODULE_DIR) . '/_vendor/');

    $loader = require VENDOR_DIR . 'autoload.php';

    Bootstrap::init($loader);

    define('ICE_BOOTSTRAP', true);
}
