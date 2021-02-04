<?php

namespace Ice\Core;

use Ice\Data\Source\Mongodb;
use Ice\Helper\Type_String;
use Ice\Model\Test;
use PHPUnit_Framework_TestCase;

class ModelTest extends PHPUnit_Framework_TestCase
{
    public function testCrud()
    {
        foreach (Module::getInstance()->getDataSourceKeys() as $dataSourceKey) {
            Logger::getInstance(__CLASS__)->info('test ' . __CLASS__ . ' ' . $dataSourceKey . '...', null, false);

            if (Type_String::startsWith($dataSourceKey, Mongodb::getClass())) {
                $id4 = '543457734521999473008348';
                $id3 = '540957798821997873008349';
                $id5 = '543454734521499443008348';
                $id6 = '543454834521499443009348';
            } else {
                $id4 = 2222;
                $id3 = 3333;
                $id5 = 5555;
                $id6 = 6666;
            }

            // INIT
            Query::getBuilder(Test::getClass())->dropTableQuery($dataSourceKey)->getQueryResult();
            Query::getBuilder(Test::getClass())->createTableQuery($dataSourceKey)->getQueryResult();

            $test1 = Test::create([
                '/name' => 'name',
                'name2' => 'test'
            ])->save(null, $dataSourceKey);

            $test2 = Test::create([
                '/name' => 'name2',
                'name2' => 'test'
            ])->save(null, $dataSourceKey);

            // SELECT
            $test3 = Test::create(['name2' => 'test2'])->find('*', $dataSourceKey);
            $this->assertNull($test3);

            $test3 = Test::create(['name2' => 'test'])->find('*', $dataSourceKey);
            $this->assertTrue($test3 instanceof Test);
            $this->assertEquals($test3->getPk(), $test1->getPk());

            $test3 = Test::create(['test_name' => 'name2'])->find('*', $dataSourceKey);
            $this->assertEquals($test3->getPk(), $test2->getPk());

            $test3 = Test::getModel($test1->getPk(), '*', $dataSourceKey);
            $this->assertEquals($test3, $test1);

            $test3 = Test::getModelBy(['/name' => 'name2'], '*', $dataSourceKey);
            $this->assertEquals($test3, $test2);

            // INSERT
            $test4 = Test::create([
                '/name' => 'name3',
                'name2' => 'test3'
            ])->save(false, $dataSourceKey);
            $test5 = Test::getModelBy(['name2' => 'test3'], '*', $dataSourceKey);
            $this->assertEquals($test4, $test5);

            if ($dataSourceKey == 'Ice\Data\Source\Mongodb/default.test') {
                continue;
            }

            // @todo убрать ресет + 'test_pk' => '/pk'
            $pk = $test4->getPk();
            Test::create([
                'test_pk' => reset($pk),
                'name2' => 'test4'
            ])->save(false, $dataSourceKey);
            $test6 = Test::getModelBy(['name2' => 'test3'], '*', $dataSourceKey);
            $this->assertNull($test6);

            $test6 = Test::getModelBy(['/name' => 'name3'], '*', $dataSourceKey);
            $this->assertEquals($test6->get('name2'), 'test4');
            $this->assertEquals($test6->getPk(), $test4->getPk());


//            $user1->set(['/name' => 'test name'])->save(null, $dataSourceKey);
//
//            $this->assertNotNull($user1);
//            $this->assertTrue($user1 instanceof Test);
//
//            $user2 = Test::create(['/name' => 'test name'])
//                ->find(['/name', 'name2'], $dataSourceKey);
//
//            $this->assertNotNull($user2);
//            $this->assertTrue($user2 instanceof Test);
//
//            $this->assertEquals($user2->get('name2'), 'test');
//
//            $this->assertEquals($user1, $user2);
//
//            $user4 = Test::getModelBy(['/name' => 'test name'], ['/name', 'name2'], $dataSourceKey);
//
//            $this->assertEquals($user2->test_name, $user4->test_name);
//
//            Test::create(['name2' => 'test'])->save();
//
////            $pkValue = $user2->getPk();
//
////            $user2->remove($dataSourceKey);
////
////            $user3 = Test::getModel($pkValue, '/pk', $dataSourceKey);
////
////            $this->assertNull($user3);
        }
    }
}
