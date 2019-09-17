<?php

namespace Ice\Bootstrap;

use Composer\Autoload\ClassLoader;
use Ice\Core\Bootstrap;

class Symfony extends Bootstrap
{
    protected static function getDefaultKey()
    {
        return MODULE_CONFIG_PATH;
    }

    public function init(ClassLoader $loader, $force = true)
    {
        parent::init($loader, $force);
        set_error_handler('Ice\Core\Logger::errorHandler');
        register_shutdown_function('Ice\Core\Logger::shutdownHandler');
    }
}
