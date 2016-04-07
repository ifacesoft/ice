<?php

namespace Ice\Bootstrap;

use Composer\Autoload\ClassLoader;
use Ice\Core\Bootstrap;

class Ice extends Bootstrap
{
    protected function __construct(array $data)
    {
        parent::__construct($data);

        set_error_handler('Ice\Core\Logger::errorHandler');
        register_shutdown_function('Ice\Core\Logger::shutdownHandler');
    }

    protected static function getDefaultKey()
    {
        return MODULE_CONFIG_PATH;
    }
}
