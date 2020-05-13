<?php

namespace Test\Ifacesoft\Ice\Cli;

use Ifacesoft\Ice\Cli\Response;
use Test\Ifacesoft\Ice\Core\ServiceTest;

class ResponseTest extends ServiceTest
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
        /** @var Response $response */
        $response = $this->create(Response::class);

        $response->send();
    }

    protected function _before()
    {
    }

    protected function _after()
    {
    }
}