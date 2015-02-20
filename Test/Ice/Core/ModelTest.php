<?php

namespace Ice\Core;

use Ice\Model\Test;
use PHPUnit_Framework_TestCase;

class ModelTest extends PHPUnit_Framework_TestCase
{
    public function testCrud()
    {
        foreach (Data_Source::getConfig()->gets() as $dataSourceClass => $config) {
            foreach ($config as $key => $schemes) {
                foreach ((array)$schemes as $scheme) {
                    $dataSourceKey = $dataSourceClass . '/' . $key . '.' . $scheme;

                    Logger::getInstance(__CLASS__)->info('test ' . __CLASS__ . ' ' . $dataSourceKey . '...', null, false);

                    Test::query()->drop($dataSourceKey);
                    Test::query()->create($dataSourceKey);

                    $user1 = Test::create([
                        '/name' => 'name',
                        'name2' => 'test'
                    ])->save([], $dataSourceKey);

                    $user1->save(['/name' => 'test name'], $dataSourceKey);

                    $this->assertNotNull($user1);
                    $this->assertTrue($user1 instanceof Test);

                    $user2 = Test::create(['/name' => 'test name'])
                        ->find(['/name', 'name2'], $dataSourceKey);

                    $this->assertNotNull($user2);
                    $this->assertTrue($user2 instanceof Test);

                    $this->assertEquals($user2->get('name2'), 'test');

                    $this->assertEquals($user1, $user2);

                    $user4 = Test::getModelBy(['/name' => 'test name'], ['/name', 'name2'], $dataSourceKey);

                    $this->assertEquals($user2->test_name, $user4->test_name);

                    $pkValue = $user2->getPk();

                    $user2->remove($dataSourceKey);

                    $user3 = Test::getModel($pkValue, '/pk', $dataSourceKey);

                    $this->assertNull($user3);
                }
            }
        }
    }
}
