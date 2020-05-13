<?php

namespace Test\Ifacesoft\Ice\Cli;

use Ice\Core\Debuger;
use Ifacesoft\Ice\Cli\Route;
use Ifacesoft\Ice\Core\Action\Version;
use Ifacesoft\Ice\Core\Router;
use Test\Ifacesoft\Ice\Core\ServiceTest;

class RouteTest extends ServiceTest
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @throws \Exception
     */
    public function testSend()
    {
        $this->create(Route::class);

        /** @var Route $route */
        $route = Route::create(
            null,
            [],
            [
                'params' => [
                    'route' => ['services' => [Router::create(null, ['uri' => '/version'])]]
                ]
            ]
        );

        $this->assertEquals('version', $route->get('routeName'));

        $this->assertEquals(Version::class, $route->get('actionClass'));
    }

    protected function _before()
    {
    }

    protected function _after()
    {
    }
}