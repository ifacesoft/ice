<?php
namespace Ice\Router;

use Ice\Core\Router;

class Symfony extends Ice
{
    public function getUrl($routeName, array $params = [])
    {
        $url = null;

        global $kernel;

        try {
            if ($url = $kernel->getContainer()->get('router')->generate($routeName, $params)) {
                return $url;
            }
        } catch (\Exception $e) {
            //
        }

        if ($url = parent::getUrl($routeName, $params)) {
            return $url;
        }

        return $url;
    }
}