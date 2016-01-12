<?php
namespace Ice\Router;

use Ice\Core\Debuger;
use Ice\Exception\RouteNotFound;

class Symfony extends Ice
{
    public function getUrl($routeName = null, array $params = [])
    {
        if (!$routeName) {
            $routeName = $this->getName();
        }

        $url = null;

        try {
            if ($url = parent::getUrl($routeName, $params)) {
                return $url;
            }
        } catch (RouteNotFound $e) {
            //
        }

        global $kernel;

        if ($url = $kernel->getContainer()->get('router')->generate($routeName, array_merge($this->getParams(), $params))) {
            return strtok($url,'?');
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