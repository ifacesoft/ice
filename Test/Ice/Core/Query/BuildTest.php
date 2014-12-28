<?php

namespace Ice\Core;

use Ice\Model\User;
use PHPUnit_Framework_TestCase;

class BuildTest extends  PHPUnit_Framework_TestCase {
    public function testActiveRecordCrud()
    {
        User::createTable();

        $user1 = User::create([
            'user_name' => 'test name',
            '/phone' => '00000000000',
            '/email' => 'qw@er.ty',
            'surname' => 'test'
        ])->insert();

        $user1->set(['surname' => 'test surname'])->update();

        $this->assertNotNull($user1);
        $this->assertTrue($user1 instanceof User);

        $user2 = User::create(['surname' => 'test surname'])
            ->select(['/name', '/phone', '/email']);

        $this->assertNotNull($user2);
        $this->assertTrue($user2 instanceof User);
        $this->assertEquals($user1, $user2);

        $user2->delete();

        $user3 = User::getModel(1, '/pk');

        $this->assertNull($user3);
    }
}
