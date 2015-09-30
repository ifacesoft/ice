<?php

namespace Ice\Router;

use Ice\Core\Route;
use Ice\Core\Router;
use Ice\Data\Provider\Router as Data_Provider_Router;

class Ice extends Router
{
    public function getUrl($routeName = null, array $params = [])
    {
        if ($url = Route::getInstance($routeName)->getUrl($params)) {
            return $url;
        }

        return Router::getLogger()->exception(
            ['Route {$0} not found', $routeName], __FILE__, __LINE__, null, null, -1, 'Ice:RouteNotFound'
        );
    }

    public function getName($url = null, $method = null)
    {
        if (!$url) {
            return Route::getInstance($this->getParams()['routeName']);
        }

        $route = Data_Provider_Router::getInstance()->getRoute($url, $method);

        return $route ? $route['routeName'] : null;
    }

    public function getParams()
    {
       return Data_Provider_Router::getInstance()->get();
    }
}
