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

if (!defined('MODULE_CONFIG_PATH')) {
    define('MODULE_CONFIG_PATH', 'Config/Ice/Core/Module.php');
}

global $loader;

if (!$loader) {
    $loader = require VENDOR_DIR . 'autoload.php';
}

try {
    $config = require MODULE_DIR . MODULE_CONFIG_PATH;

    foreach ($config['module']['ignorePatterns'] as $ignorePattern) {
        if (preg_match($ignorePattern, \Ice\Core\Request::uri(true))) {
            return;
        }
    }

    require_once ICE_DIR . 'Source/Ice/Core/DataProvider.php';
    require_once ICE_DIR . 'Source/Ice/Core/Module.php';
    require_once ICE_DIR . 'Source/Ice/Helper/Date.php';
    require_once ICE_DIR . 'Source/Ice/Helper/Config.php';
    require_once ICE_DIR . 'Source/Ice/DataProvider/File.php';
    require_once ICE_DIR . 'Source/Ice/DataProvider/Registry.php';
    require_once ICE_DIR . 'Source/Ice/DataProvider/Apc.php';
    require_once ICE_DIR . 'Source/Ice/DataProvider/Redis.php';
    require_once ICE_DIR . 'Source/Ice/DataProvider/Repository.php';
    require_once ICE_DIR . 'Source/Ice/Core/Render.php';
    require_once ICE_DIR . 'Source/Ice/Helper/Api/Client/Yandex/Translate.php';

    \Ice\Core\Bootstrap::getInstance($config['module']['bootstrapClass'], null, ['loader' => $loader]);

    require_once ICE_DIR . 'functions.php';
} catch (Exception $e) {
    echo '<span style="font-weight: bold;">Bootstrapping failed: ' .
        str_replace(MODULE_DIR, '', $e->getMessage()) .
        '</span><br>';
    echo nl2br(str_replace(MODULE_DIR, '', $e->getTraceAsString()) . "\n");
    die('Terminated. Bye-bye...' . "\n");
}