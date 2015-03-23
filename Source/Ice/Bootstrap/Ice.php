<?php

namespace Ice\Bootstrap;

use Composer\Autoload\ClassLoader;
use Ice\Core\Bootstrap;

class Ice extends Bootstrap
{
    public function init(ClassLoader $loader, $force = false)
    {
        parent::init($loader, $force);
    }

    protected static function getDefaultKey()
    {
        return MODULE_CONFIG_PATH;
    }
}