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

if (!defined('VENDOR_DIR')) {
    define('VENDOR_DIR', dirname(__DIR__) . '/_vendor/');
}

require_once __DIR__ . '/bootstrap.php';

\Ice\App::run();