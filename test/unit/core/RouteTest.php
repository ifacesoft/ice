<?php

namespace Test\Ifacesoft\Ice\Core;

use Ice\Core\Debuger;
use Ifacesoft\Ice\Core\Route;

class RouteTest extends ServiceTest
{
    /**
     * @throws \Exception
     */
    public function testRouter()
    {
        /** @var Route $route */
        $this->create(Route::class, 'ifacesoft_ice_core_version');

        /** @var Route $route */
        $route = Route::create('ifacesoft_ice_core_version');

        $action = $route->get('action');

        Debuger::dump($action);
    }
}