<?php

namespace Ice\Core;

use Ice\Model\Test;
use Ice\Model\User;
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

        User::createTable();

        $user1 = User::create([
            'user_name' => 'test name',
            '/phone' => '00000000000',
            '/email' => 'qw@er.ty',
            'surname' => 'test'
        ])->save();

        $user1->save(['surname' => 'test surname']);

        $this->assertNotNull($user1);
        $this->assertTrue($user1 instanceof User);

        $user2 = User::create(['surname' => 'test surname'])
            ->find(['/name', '/phone', '/email']);

        $this->assertNotNull($user2);
        $this->assertTrue($user2 instanceof User);
        $this->assertEquals($user1, $user2);

        $user2->remove();

        $user3 = User::getModel(1, '/pk');

        $this->assertNull($user3);
    }
}
