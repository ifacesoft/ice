<?php

namespace Ice\Bootstrap;

use Composer\Autoload\ClassLoader;
use Ice\Core\Bootstrap;
use Ice\Core\Module;
use Ice\Security\Ice as Security_Ice;
use Ice\Router\Ice as Router_Ice;

class Ice extends Bootstrap
{
    protected static function getDefaultKey()
    {
        return MODULE_CONFIG_PATH;
    }

    protected function init(array $params)
    {
        parent::init($params);

        set_error_handler('Ice\Core\Logger::errorHandler');
        register_shutdown_function('Ice\Core\Logger::shutdownHandler');
    }
}
