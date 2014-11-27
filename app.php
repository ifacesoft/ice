<?php
/**
 * @file Application run script
 *
 * Run ice application
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @version 0.0
 * @since 0.0
 */

use Ice\Bootstrap;

if (!defined('MODULE_DIR')) {
    define('MODULE_DIR', __DIR__ . '/');
}

$bootstrapPath = MODULE_DIR . '../Ice/Source/Ice/Bootstrap.php';

if (!file_exists($bootstrapPath)) {
    die("\033[01;31mIce bootstrap not found! (" . $bootstrapPath . ")\033[0m\n");
}

include_once $bootstrapPath;

Bootstrap::init();
Ice::getInstance(dirname(MODULE_DIR))->run()->flush();