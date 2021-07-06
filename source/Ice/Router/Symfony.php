<?php
namespace Ice\Router;

use Ice\Core\Request;
use Ice\Exception\RouteNotFound;

class Symfony extends Ice
{
    public function getUrl($routeOptions = null, $force = false)
    {
        if (!$routeOptions) {
            $routeOptions = $this->getName();
        }

        try {
            if (!$force) {
                return parent::getUrl($routeOptions);
            }
        } catch (RouteNotFound $e) {
            //
        }

        $url = null;

        list($routeName, $routeParams, $urlWithGet, $urlWithDomain, $replaceContext) = array_pad((array)$routeOptions, 5, false);

        global $kernel;

        if ($kernel) {
            $url = $kernel
                ->getContainer()
                ->get('router')
                ->generate($routeName, array_merge((array)$this->getParams($force), (array)$routeParams)); // todo: должно быть как строчкой ниже
//                ->generate($routeName, $routeParams);
        }

        if (!$urlWithGet) {
            $url = strtok($url, '?');
        }

        if ($urlWithDomain) {
            $domain = $urlWithDomain === true
                ? Request::protocol() . Request::host()
                : $urlWithDomain;

            return $domain . $url;
        }

        return $url;
    }

    public function getName($url = null, $method = null)
    {
        try {
            return parent::getName($url, $method);
        } catch (\Exception $e) {
            //
        }

        global $kernel;

        return $kernel->getContainer()->get('request_stack')->getCurrentRequest()->get('_route');
    }

    /**
     * @param bool $force
     * @return array
     */
    public function getParams($force = false)
    {
        if (!$force) {
            try {
                return parent::getParams();
            } catch (\Exception $e) {
                //
            }
        }

        global $kernel;

        return $kernel->getContainer()->get('request_stack')->getCurrentRequest()->attributes->get('_route_params');
    }
}