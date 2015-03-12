<?php

namespace Ice\Core;

use Ice\Model\Test;
use PHPUnit_Framework_TestCase;

class BuilderTest extends PHPUnit_Framework_TestCase
{
    public function testActiveRecordCrud()
    {
        Test::query()->drop();
        Test::query()->create();

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

        $user1 = Test::create([
            'test_name' => 'name2',
        ])->save();

        $this->assertEquals($user1->get(), [
            'test_pk' => 2,
            'test_name' => 'name2',
            'name2' => null
        ]);

        $user1->set(['name2' => 'test2'])->save();

        $this->assertEquals($user1->get(), [
            'test_pk' => 2,
            'test_name' => 'name2',
            'name2' => 'test2',
        ]);

        $this->assertNotNull($user1);
        $this->assertTrue($user1 instanceof Test);

        $user2 = Test::create(['name2' => 'test2'])
            ->find(['/name']);

        $this->assertNotNull($user2);
        $this->assertTrue($user2 instanceof Test);
        $this->assertEquals($user1, $user2);

        $user2->remove();

        $user3 = Test::getModel(2, '/pk');

        $this->assertNull($user3);
    }
}
