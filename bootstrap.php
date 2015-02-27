<?php

if (defined('BOOTSTRAP')) {
    return;
}

define('BOOTSTRAP', true);

use Ice\Bootstrap;

if (!defined('MODULE_DIR')) {
    define('MODULE_DIR', __DIR__ . '/');
}

$bootstrapPath = MODULE_DIR . '../Ice/Source/Ice/Bootstrap.php';

if (!file_exists($bootstrapPath)) {
    $bootstrapPath = MODULE_DIR . 'Source/Ice/Bootstrap.php';

    if (!file_exists($bootstrapPath)) {
        die("\033[01;31mIce bootstrap not found! (" . $bootstrapPath . ")\033[0m\n");
    }
}

require_once $bootstrapPath;

Bootstrap::init();
