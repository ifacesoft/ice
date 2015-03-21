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

define('ICE_DIR', __DIR__ . '/');

require_once ICE_DIR . 'Source/Ice/Bootstrap.php';

\Ice\App::run();