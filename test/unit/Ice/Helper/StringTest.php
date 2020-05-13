<?php namespace Ice\Helper;

use PHPUnit\Framework\TestCase;

class StringTest extends TestCase
{
    public function testStartsWith()
    {
        $this->assertTrue(Type_String::startsWith('bi_blog', 'bi_'));
        $this->assertFalse(Type_String::startsWith('bi_blog', 'bi-'));
    }

    public function testEndsWith()
    {
        $this->assertTrue(Type_String::endsWith('bi_blog', '_blog'));
        $this->assertFalse(Type_String::endsWith('bi_blog', '-blog'));
    }
}