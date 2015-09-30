<?php

namespace Ice\Core;

use Ice\Core;
use Ice\Exception\Error;

abstract class Router extends Container
{
    use Core;

    private static $defaultClassKey = null;

    /**
     * @param string $key
     * @param int $ttl
     * @param array $params
     * @return Router
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 2.0
     * @since   0.0
     */
    public static function getInstance($key = null, $ttl = null, array $params = [])
    {
        return parent::getInstance($key, $ttl, $params);
    }

    protected function init(array $params) {
        if (Router::$defaultClassKey === null) {
            Router::$defaultClassKey = get_class($this);
            return;
        }

        throw new Error('Router already initialized');
    }

    protected static function getDefaultClassKey()
    {
        return Router::$defaultClassKey;
    }

    protected static function getDefaultKey()
    {
        return 'default';
    }

    public abstract function getUrl($routeName = null, array $params = []);

    public abstract function getName($url = null, $method = null);

    public abstract function getParams();
}