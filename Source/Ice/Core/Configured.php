<?php

namespace Ice\Core;

use Ice\DataProvider\Repository;
use Ice\Helper\Object;

trait Configured
{
    /**
     * Get action config
     *
     * @return Config
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.3
     * @since   1.1
     */
    public static function getConfig()
    {
        /** @var Configured $class */
        $class = get_called_class();

        $baseClass = Object::getBaseClass($class);

        $repository = Repository::getInstance($baseClass, $class);

        if ($config = $repository->get('config')) {
            return $config;
        }

        $config = Config::create(
            $class,
            Config::getInstance($class, null, false, -1, $class::config())->gets()
        );

        return $repository->set(['config' => $config])['config'];
    }
}