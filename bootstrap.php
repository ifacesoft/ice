<?php
use Ice\Bootstrap;

if (!defined('ICE_BOOTSTRAP')) {

    define('MODULE_DIR', php_sapi_name() == 'cli' ? dirname(getcwd()) . '/' : dirname(dirname($_SERVER['PHP_SELF'])));
    define('MODULE_CONFIG_PATH', 'Config/Ice/Core/Module.php');
    define('VENDOR', basename(dirname(MODULE_DIR)) . '/' . basename(MODULE_DIR));
    define('VENDOR_DIR', strstr(MODULE_DIR, VENDOR, true));

    $autoloadPath = VENDOR_DIR . 'composer/' . VENDOR . '/autoload_real.php';

    if (file_exists($autoloadPath)) {
        require $autoloadPath;
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

    define('ICE_BOOTSTRAP', true);
}
