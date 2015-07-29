<?php
namespace Ice\Router;

class Symfony extends Ice
{
    public function getUrl($routeName, array $params = [])
    {
        global $kernel;
        $url = $securityAuthorizationChecker = $kernel->getContainer()->get('router')->generate($routeName, $params);

        if (!$url) {
            return parent::getUrl($routeName, $params);
        }
    }
}