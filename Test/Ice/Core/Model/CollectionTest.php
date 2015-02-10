<?php

namespace Ice\Core;

use Ice\Model\Test;
use PHPUnit_Framework_TestCase;

class Model_CollectionTest extends PHPUnit_Framework_TestCase
{
    public function testCollection()
    {
        foreach (Data_Source::getConfig()->gets('default') as $dataSourceClass => $scheme) {
            $dataSourceKey = $dataSourceClass . '/default.' . $scheme;

//        $dataSourceKey = 'Ice:Mysqli/default.test';
//        $dataSourceKey = 'Ice:Mongodb/default.test';

        Test::dropTable($dataSourceKey);
        Test::createTable($dataSourceKey);

        $row = [
            '/name' => 'name11',
            'name2' => 'name21'
        ];

        $collectionRows = [
            [
                'test_pk' => '543457734521999473008348',
                '/name' => 'name12',
                'name2' => 'name22'
            ],
            [
                'test_pk' => '540957798821997873008349',
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
                'test_pk' => '543454734521499443008348',
                '/name' => 'name15',
                'name2' => 'name25'
            ],
            [
                'test_pk' => '543454834521499443009348',
                '/name' => 'name16',
                'name2' => 'name26'
            ]
        ];

        $testCollection = Test::getCollection(['/name', 'name2'], [1, 1000, 0], $dataSourceKey);

        $this->assertEquals($testCollection->getRows(), Model_Collection::create(Test::getClass())->getRows());

        $testCollection->add(Test::create($row))->save($dataSourceKey);

        $firstId = $testCollection->first()->getPk();

        $this->assertEquals($testCollection->getRows(), Test::getCollection('*', [1, 1000, 0], $dataSourceKey)->getRows());

        $testCollection->add(Model_Collection::create(Test::getClass(), $collectionRows))->save($dataSourceKey, true);

        $this->assertEquals(
            $testCollection->getRows(),
            [
                reset($firstId) => [
                    'test_pk' => reset($firstId),
                    'test_name' => 'name11',
                    'name2' => 'name21'
                ],
                '543457734521999473008348' => [
                    'test_pk' => '543457734521999473008348',
                    'test_name' => 'name12',
                    'name2' => 'name22'
                ],
                '540957798821997873008349' => [
                    'test_pk' => '540957798821997873008349',
                    'test_name' => 'name13',
                    'name2' => 'name23'
                ]
            ]
        );

        $testCollection->add(Test::create()->save($modelRow, $dataSourceKey));

        $forthPk = Test::getCollection('*', [1, 1000, 0], $dataSourceKey)->last()->getPk();

        $this->assertEquals(
            [
                reset($firstId) => [
                    'test_pk' => reset($firstId),
                    'test_name' => 'name11',
                    'name2' => 'name21'
                ],
                '543457734521999473008348' => [
                    'test_pk' => '543457734521999473008348',
                    'test_name' => 'name12',
                    'name2' => 'name22'
                ],
                '540957798821997873008349' => [
                    'test_pk' => '540957798821997873008349',
                    'test_name' => 'name13',
                    'name2' => 'name23'
                ],
                reset($forthPk) => [
                    'test_pk' => reset($forthPk),
                    'test_name' => 'name14',
                    'name2' => 'name24'
                ]
            ],
            Test::getCollection('*', [1, 1000, 0], $dataSourceKey)->getRows()
        );

        $this->assertEquals($testCollection->getRows(), Test::getCollection('*', [1, 1000, 0], $dataSourceKey)->getRows());

        Model_Collection::create(Test::getClass())->add($rows)->save($dataSourceKey);

        $testCollection = Test::getCollection('*', [1, 1000, 0], $dataSourceKey);

        $this->assertEquals(
            [
                reset($firstId) => [
                    'test_pk' => reset($firstId),
                    'test_name' => 'name11',
                    'name2' => 'name21'
                ],
                '543457734521999473008348' => [
                    'test_pk' => '543457734521999473008348',
                    'test_name' => 'name12',
                    'name2' => 'name22'
                ],
                '540957798821997873008349' => [
                    'test_pk' => '540957798821997873008349',
                    'test_name' => 'name13',
                    'name2' => 'name23'
                ],
                reset($forthPk) => [
                    'test_pk' => reset($forthPk),
                    'test_name' => 'name14',
                    'name2' => 'name24'
                ],
                '543454734521499443008348' => [
                    'test_pk' => '543454734521499443008348',
                    'test_name' => 'name15',
                    'name2' => 'name25'
                ],
                '543454834521499443009348' => [
                    'test_pk' => '543454834521499443009348',
                    'test_name' => 'name16',
                    'name2' => 'name26'
                ]
            ],
            $testCollection->getRows()
        );

        Test::query()->inPk([reset($firstId), '540957798821997873008349', '543454834521499443009348'])->select('/name', null, null, null, $dataSourceKey)->getModelCollection()->remove($dataSourceKey);

        $this->assertNotEquals($testCollection->getRows(), Test::getCollection('*', [1, 1000, 0], $dataSourceKey)->getRows());

        $this->assertEquals($testCollection->reload(), Test::getCollection('*', [1, 1000, 0], $dataSourceKey));

        $this->assertEquals($testCollection->first(), Test::getModel('543457734521999473008348', '*', $dataSourceKey));

//        $this->assertEquals($testCollection->last(), Test::getModel('543454734521499443008348', '*', $dataSourceKey));
//
//        $this->assertEquals($testCollection->getRow(reset($forthPk)), [
//            'test_pk' => reset($forthPk),
//            'test_name' => 'name14',
//            'name2' => 'name24'
//        ]);
//
//        $this->assertEquals($testCollection->getKeys(), ['543457734521999473008348', reset($forthPk), '543454734521499443008348']);
//
//        $this->assertEquals($testCollection->get(reset($forthPk)), Test::getModel(reset($forthPk), '*', $dataSourceKey));
//
//        foreach ($testCollection as $test) {
//            $test->save(['/name' => ''], $dataSourceKey);
//        }
//        $testCollection->reload(); // todo: научить чтобы без релоада работало
//        $this->assertEquals($testCollection->column('test_name'), ['543457734521999473008348' => '', reset($forthPk) => '', '543454734521499443008348' => '']);
    }
    }
}