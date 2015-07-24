<?php
namespace Ice\Router;

use Ice\Core\Router;

class Symfony extends Router
{
    public function getUrl($routeName, array $params = [])
    {
        global $kernel;
        return $securityAuthorizationChecker = $kernel->getContainer()->get('router')->generate($routeName, $params);
    }
}