<?php

namespace Ice\Core;

use Ice\Core;
use Ice\Data\Provider\Repository;
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
     * @version 2.0
     * @since   2.0
     */
    public static function getConfig()
    {
        $class = self::getClass();

        $baseClass = Object::getBaseClass($class);

        $repository = Repository::getInstance($baseClass, $class);

        if ($config = $repository->get('config')) {
            return $config;
        }

        $config = Config::create(
            $class,
            array_merge_recursive($class::config(), Config::getInstance($class, null, false, -1)->gets())
        );

        return $repository->set('config', $config);
    }
}