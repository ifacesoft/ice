<?php

namespace Ice\Core;

use Ice\Model\Test;
use PHPUnit_Framework_TestCase;

class Model_CollectionTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        Test::dropTable();
        Test::createTable();
    }

    public function testCollection()
    {
        $row = [
            '/name' => 'name11',
            'name2' => 'name21'
        ];

        $collectionRows = [
            [
                'test_pk' => 2,
                '/name' => 'name12',
                'name2' => 'name22'
            ],
            [
                'test_pk' => 3,
                '/name' => 'name13',
                'name2' => 'name23'
            ]
        ];

        $modelRow = [
            '/name' => 'name14',
            'name2' => 'name24'
        ];

        $rows = [
            [
                'test_pk' => 5,
                '/name' => 'name15',
                'name2' => 'name25'
            ],
            [
                'test_pk' => 6,
                '/name' => 'name16',
                'name2' => 'name26'
            ]
        ];

        $testCollection = Test::getCollection(['/name', 'name2']);

        $this->assertEquals($testCollection->getRows(), Model_Collection::create(Test::getClass())->getRows());

        $testCollection->add(Test::create($row))->save();

        $this->assertEquals($testCollection->getRows(), Test::getCollection('*')->getRows());

        $testCollection->add(Model_Collection::create(Test::getClass(), $collectionRows))->save(null, true);

        $this->assertEquals(
            $testCollection->getRows(),
            [
                1 => [
                    'test_pk' => 1,
                    'test_name' => 'name11',
                    'name2' => 'name21'
                ],
                2 => [
                    'test_pk' => 2,
                    'test_name' => 'name12',
                    'name2' => 'name22'
                ],
                3 => [
                    'test_pk' => 3,
                    'test_name' => 'name13',
                    'name2' => 'name23'
                ]
            ]
        );

        $testCollection->add(Test::create($modelRow)->save());

        $this->assertEquals(
            [
                1 => [
                    'test_pk' => 1,
                    'test_name' => 'name11',
                    'name2' => 'name21'
                ],
                2 => [
                    'test_pk' => 2,
                    'test_name' => 'name12',
                    'name2' => 'name22'
                ],
                3 => [
                    'test_pk' => 3,
                    'test_name' => 'name13',
                    'name2' => 'name23'
                ],
                4 => [
                    'test_pk' => 4,
                    'test_name' => 'name14',
                    'name2' => 'name24'
                ]
            ],
            Test::getCollection('*')->getRows()
        );

        $this->assertEquals($testCollection->getRows(), Test::getCollection('*')->getRows());

        Model_Collection::create(Test::getClass())->add($rows)->save();

        $testCollection = Test::getCollection('*');

        $this->assertEquals(
            [
                1 => [
                    'test_pk' => 1,
                    'test_name' => 'name11',
                    'name2' => 'name21'
                ],
                2 => [
                    'test_pk' => 2,
                    'test_name' => 'name12',
                    'name2' => 'name22'
                ],
                3 => [
                    'test_pk' => 3,
                    'test_name' => 'name13',
                    'name2' => 'name23'
                ],
                4 => [
                    'test_pk' => 4,
                    'test_name' => 'name14',
                    'name2' => 'name24'
                ],
                5 => [
                    'test_pk' => 5,
                    'test_name' => 'name15',
                    'name2' => 'name25'
                ],
                6 => [
                    'test_pk' => 6,
                    'test_name' => 'name16',
                    'name2' => 'name26'
                ]
            ],
            $testCollection->getRows()
        );

        Test::query()->inPk([1, 3, 6])->select('/name')->getModelCollection()->remove();

        $this->assertNotEquals($testCollection->getRows(), Test::getCollection('*')->getRows());

        $this->assertEquals($testCollection->reload(), Test::getCollection('*'));

        $this->assertEquals($testCollection->first(), Test::getModel(2, '*'));

        $this->assertEquals($testCollection->last(), Test::getModel(5, '*'));

        $this->assertEquals($testCollection->getRow(4), [
            'test_pk' => 4,
            'test_name' => 'name14',
            'name2' => 'name24'
        ]);

        $this->assertEquals($testCollection->getKeys(), [2, 4, 5]);

        $this->assertEquals($testCollection->get(4), Test::getModel(4, '*'));
    }
}