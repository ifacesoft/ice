<?php

namespace Ice\Core;

use Ice\Model\Test;
use PHPUnit_Framework_TestCase;

class BuilderTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        Test::dropTable();
        Test::createTable();
    }

    public function testActiveRecordCrud()
    {
        $userRow = Test::query()
            ->insert([
                '/name' => 'name',
                'name2' => 'test'
            ])->getRow();

        $this->assertEquals($userRow, [
            '/name' => 'name',
            'name2' => 'test',
            'test_pk' => 1
        ]);
    }
}
