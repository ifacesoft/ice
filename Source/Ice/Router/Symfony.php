<?php
namespace Ice\Router;

use Ice\Core\Debuger;
use Ice\Exception\RouteNotFound;

class Symfony extends Ice
{
    public function getUrl($routeName = null, array $params = [], $withGet = false)
    {
        if (!$routeName) {
            $routeName = $this->getName();
        }

        $routeName = (array) $routeName;
        
        $routeParams = [];
        $urlWithGet = false;
        
        $url = null;

        try {
            if (count($routeName) == 3) {
                list($routeName, $routeParams, $urlWithGet) = $routeName;
                $routeParams = array_merge($params, $routeParams);
            } elseif (count($routeName) == 2) {
                list($routeName, $routeParams) = $routeName;
                $routeParams = array_merge($params, $routeParams);
                $urlWithGet = $withGet;
            } else {
                $routeName = reset($routeName);
                $routeParams = $params;
                $urlWithGet = $withGet;
            }

            if ($url = parent::getUrl($routeName, $routeParams, $urlWithGet)) {
                return $url;
            }
        } catch (RouteNotFound $e) {
            //
        }

        global $kernel;

        if ($url = $kernel->getContainer()->get('router')->generate($routeName, array_merge($this->getParams(), $routeParams))) {
            return $urlWithGet ? $url : strtok($url,'?');
        }

        return $url;
    }

    public function getName($url = null, $method = null)
    {
        global $kernel;

        try {
            if ($routeName = $kernel->getContainer()->get('request')->get('_route')) {
                return $routeName;
            }
        } catch (\Exception $e) {
            //
        }

        if ($routeName = parent::getName($url, $method)) {
            return $routeName;
        }

        return $routeName;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        global $kernel;

        return $kernel->getContainer()->get('request')->attributes->get('_route_params');
    }
}