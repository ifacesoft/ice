<?php
/**
 * @file
 * Directory index file
 *
 * Run and flush ice application
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @version 0.0
 * @since 0.0
 */

define('VENDOR_DIR', dirname(dirname(__DIR__)) . '/_vendor/Ice/');

require_once dirname(__DIR__) . '/app.php';