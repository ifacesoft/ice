<?php
use Ice\Bootstrap;

define('MODULE_DIR', __DIR__ . '/');
define('MODULE_CONFIG_PATH', 'Config/Ice/Core/Module.php');
define('VENDOR_DIR', realpath('../_vendor/Ice') . '/');

$loader = require VENDOR_DIR . 'autoload.php';

Bootstrap::init($loader);
