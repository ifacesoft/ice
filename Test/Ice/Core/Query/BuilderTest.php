<?php

namespace Ice\Core;

use Ice\Model\Test;
use PHPUnit_Framework_TestCase;

class BuilderTest extends PHPUnit_Framework_TestCase
{
    public function testActiveRecordCrud()
    {
        Query::getBuilder(Test::getClass())->dropTableQuery()->getQueryResult();
        Query::getBuilder(Test::getClass())->createTableQuery()->getQueryResult();

        $userRow = Query::getBuilder(Test::getClass())
            ->insertQuery([
                '/name' => 'name',
                'name2' => 'test'
            ])->getRow();

        $this->assertEquals($userRow, [
            '/name' => 'name',
            'name2' => 'test',
            'test_pk' => 1
        ]);

        $test1 = Test::create([
            'test_name' => 'name2',
        ])->save();

        $this->assertEquals($test1->get(), [
            'test_pk' => 2,
            'test_name' => 'name2',
            'name2' => null
        ]);

        $test1->set(['name2' => 'test2']);

        $test1->save();

        $this->assertEquals($test1->get(), [
            'test_pk' => 2,
            'test_name' => 'name2',
            'name2' => 'test2',
        ]);

        $this->assertNotNull($test1);
        $this->assertTrue($test1 instanceof Test);

        $test2 = Test::create(['name2' => 'test2'])
            ->find(['/name']);

        $this->assertNotNull($test2);
        $this->assertTrue($test2 instanceof Test);
        $this->assertEquals($test1, $test2);

        $test2->remove();

        $test3 = Test::getModel(2, '/pk');

        $this->assertNull($test3);
    }
}
