<?php
namespace Ice\Helper;

use PHPUnit\Framework\TestCase;

class PhpTest extends TestCase
{
    public function testPassingByReference()
    {
        $a = 2;
        $b = &$a;
        $b = 5;
        $this->assertEquals($a, 5);

        Php::passingByReference($b);

        $this->assertNotEquals($a, 5);
    }
}