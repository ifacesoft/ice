<?php

namespace Test\Ifacesoft\Ice\Cli;

use Ifacesoft\Ice\Cli\App;
use Test\Ifacesoft\Ice\Core\ServiceTest;

class AppTest extends ServiceTest
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
        /** @var App $app */
        $app = $this->create(App::class);
    }

    protected function _before()
    {
    }

    protected function _after()
    {
    }
}