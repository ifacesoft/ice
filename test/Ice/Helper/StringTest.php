<?php namespace Ice\Helper;

use PHPUnit_Framework_TestCase;

class StringTest extends PHPUnit_Framework_TestCase
{
    public function testStartsWith()
    {
        $this->assertTrue(String::startsWith('bi_blog', 'bi_'));
        $this->assertFalse(String::startsWith('bi_blog', 'bi-'));
    }

    public function testEndsWith()
    {
        $this->assertTrue(String::endsWith('bi_blog', '_blog'));
        $this->assertFalse(String::endsWith('bi_blog', '-blog'));
    }
}