<?php
/**
 * @file Application run script
 *
 * Run ice application
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @version 0.2
 * @since 0.0
 */

if (file_exists(__DIR__ . '/bootstrap.php')) {
    require_once __DIR__ . '/bootstrap.php';
} else {
    define('VENDOR_DIR', realpath(__DIR__ . '/Var/vendor') . '/');

    require_once VENDOR_DIR . 'ifacesoft/ice/bootstrap.php';
}

\Ice\App::run();