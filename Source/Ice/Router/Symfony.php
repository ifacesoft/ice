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

        global $kernel;

        try {
            if ($url = $kernel->getContainer()->get('router')->generate($routeName, $params)) {
                return $url;
            }
        } catch (\Exception $e) {
            //
        }

        try {
            if ($url = parent::getUrl($routeName, $params)) {
                return $url;
            }
        } catch (RouteNotFound $e) {
            //
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
}