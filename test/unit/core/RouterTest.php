<?php

namespace Test\Ifacesoft\Ice\Core;

use Ifacesoft\Ice\Core\Router;

class RouterTest extends ServiceTest
{
    /**
     * @throws \Exception
     */
    public function testRouter()
    {
        /** @var Router $router */
       $this->create(Router::class, 'cli');

        /** @var Router $router */
        $router = Router::create('cli');

        $this->assertEquals('run', $router->get('uri'));

        $this->assertEquals('CLI', $router->get('method'));

        $this->assertNull($router->get('route'));

        $this->assertNotNull($router->get('route', ['uri' => '/version']));

        $this->create(Router::class, 'http');
    }
}