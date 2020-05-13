<?php

namespace Test\Ifacesoft\Ice\Cli;

use Ifacesoft\Ice\Cli\Request;
use Test\Ifacesoft\Ice\Core\ServiceTest;

class RequestTest extends ServiceTest
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @throws \Exception
     */
    public function testMain()
    {
        /** @var Request $request */
        $request = $this->create(Request::class);

        $this->assertEquals(Request::METHOD, $request->get('method'));
        $this->assertEquals('run', $request->get('uri'));

        $request->set(['uri' => 'version1']);

        $this->assertEquals('version1', $request->get('uri'));

        $request = Request::create(null, ['uri' => 'version2']);

        $this->assertEquals('version2', $request->get('uri'));
    }

    protected function _before()
    {
    }

    protected function _after()
    {
    }
}