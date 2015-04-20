<?php

namespace Ice\Action;

use PHPUnit_Framework_TestCase;

class DeployTest extends PHPUnit_Framework_TestCase
{
    public function testRun()
    {
        $this->assertEmpty(Deploy::call()->getErrors());
    }
}