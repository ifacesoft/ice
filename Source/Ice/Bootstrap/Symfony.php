<?php

namespace Ice\Bootstrap;

use Composer\Autoload\ClassLoader;
use Ice\Core\Bootstrap;
use Ice\Core\Debuger;
use Ice\Core\Module;
use Ice\Core\Router;
use Ice\Core\Security;
use Ice\Security\Symfony as Security_Symfony;
use Ice\Router\Symfony as Router_Symfony;

class Symfony extends Bootstrap
{
    protected static function getDefaultKey()
    {
        return MODULE_CONFIG_PATH;
    }

    public function init(array $data)
    {
        $data['force'] = true;

        parent::init($data);

//        set_error_handler('Ice\Core\Logger::errorHandler');
//        register_shutdown_function('Ice\Core\Logger::shutdownHandler');
    }
}
