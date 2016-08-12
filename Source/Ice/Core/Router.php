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
     * @param null $routeName
     * @return mixed
     */
    public abstract function getUrl($routeName = null);

    public abstract function getName($url = null, $method = null);

    public abstract function getParams();
}