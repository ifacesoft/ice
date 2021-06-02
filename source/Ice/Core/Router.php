<?php

namespace Ice\Core;

use Ice\Core;

abstract class Router extends Container
{
    use Core;

    /**
     * @param string $instanceKey
     * @param int $ttl
     * @param array $params
     * @return Router|Container
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   0.0
     * @throws Exception
     */
    public static function getInstance($instanceKey = null, $ttl = null, array $params = [])
    {
        return parent::getInstance($instanceKey, $ttl, $params);
    }

    protected static function getDefaultKey()
    {
        return 'default';
    }

    /**
     * @param null $routeOptions
     * @param bool $force
     * @return mixed
     */
    public abstract function getUrl($routeOptions = null, $force = false);

    public abstract function getName($url = null, $method = null);

    public abstract function getParams($force = false);
}