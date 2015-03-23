<?php

namespace Ice\Bootstrap;

use Composer\Autoload\ClassLoader;
use Ice\Core\Bootstrap;

class Symfony extends Bootstrap {
   public function init(ClassLoader $loader, $force = true)
    {
        define('MODULE_CONFIG_PATH', $this->getModuleConfigPath());

        parent::init($loader, $force);
    }

    protected static function getDefaultKey()
    {
        return MODULE_CONFIG_PATH;
    }
}