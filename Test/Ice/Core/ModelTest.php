<?php

namespace Ice\Core;

use Ice\Model\Test;
use PHPUnit_Framework_TestCase;

class ModelTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        Test::dropTable();
        Test::createTable();
    }

    public function testActiveRecordCrud()
    {
        $user1 = Test::create([
            '/name' => 'name',
            'name2' => 'test'
        ])->save();

        $user1->set(['/name' => 'test name'])->save();

        $this->assertNotNull($user1);
        $this->assertTrue($user1 instanceof Test);

        $user2 = Test::create(['/name' => 'test name'])
            ->find(['/name', 'name2']);

        $user4 = Test::getModelBy(['/name' => 'test name'], ['/name', 'name2']);

        $this->assertEquals($user2->get('/name'), 'test name');

        $this->assertNotNull($user2);
        $this->assertTrue($user2 instanceof Test);
        $this->assertEquals($user1, $user2);
        $this->assertEquals($user2->test_name, $user4->test_name);

        $user2->remove();

        $user3 = Test::getModel(1, '/pk');

        $this->assertNull($user3);
    }
}
