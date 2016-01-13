<?php

namespace Ice\Router;

use Ice\Core\Route;
use Ice\Core\Router;
use Ice\Data\Provider\Router as Data_Provider_Router;

class Ice extends Router
{
    public function getUrl($routeName = null, array $params = [], $withGet = false)
    {
        if ($url = Route::getInstance($routeName)->getUrl(array_merge($this->getParams(), $params))) {
            return $url;
        }

        return null;
    }

    public function getName($url = null, $method = null)
    {
        if (!$url) {
            return Route::getInstance($this->getParams()['routeName']);
        }

        $route = Data_Provider_Router::getInstance()->getRoute($url, $method);

        return $route ? $route['routeName'] : null;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return Data_Provider_Router::getInstance()->get();
    }
}
