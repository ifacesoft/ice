<?php

namespace Test\Ifacesoft\Ice\Core;

use Ifacesoft\Ice\Cli\Request;

class RequestTest extends ServiceTest
{
    /**
     * @throws \Exception
     */
    public function testRouter()
    {
        $this->create(Request::class);

        /** @var Request $request */
        $request = Request::create();

        $this->assertEquals('run', $request->get('uri'));
        $this->assertEquals(Request::METHOD, $request->get('method'));
//        $this->assertEquals('ru_RU.UTF-8', $request->get('locale'));
        $this->assertEquals('Europe/Moscow', $request->get('timezone'));
    }
}