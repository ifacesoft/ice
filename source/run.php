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

use Ice\App;

require_once __DIR__ . '/bootstrap.php';

try {
    App::run();
} catch (Exception | Throwable $e) {
    echo \Ifacesoft\Ice\Core\Domain\Exception\Error::create(__FILE__, 'Application failed', [], $e)->get('html');

    die("\033[0;31m" . $e->getMessage() . "\n" . "\033[0m");
}
