<?php
namespace Ice\Core\Action;

use Ice\Core\Action_Context;
use Ice\Core\Exception;
use PHPUnit_Framework_TestCase;

class Action_ContextTest extends PHPUnit_Framework_TestCase
{
    public function testAddAction()
    {
        $actionContext = new Action_Context();

        $data = '';
        $actionContext->addAction($data);

        $this->assertEquals($actionContext->getActions(), []);

        $data = 'Ice:Action1';
        $actionContext->addAction($data);

        $this->assertEquals(
            $actionContext->getActions(),
            [
                'Ice:Action1' => [
                    0 => []
                ]
            ]
        );

        $data = ['Ice:Action1' => []];
        $actionContext->addAction($data);

        $this->assertEquals(
            $actionContext->getActions(),
            [
                'Ice:Action1' => [
                    0 => [],
                    1 => [],
                ]
            ]
        );

        $data = ['action2' => 'Ice:Action2'];
        $actionContext->addAction($data);

        $this->assertEquals(
            $actionContext->getActions(),
            [
                'Ice:Action1' => [
                    0 => [],
                    1 => [],
                ],
                'Ice:Action2' => [
                    'action2' => []
                ]
            ]
        );

        $data = ['Ice:Action3' => ['param1' => 'value1']];
        $actionContext->addAction($data);

        $this->assertEquals(
            $actionContext->getActions(),
            [
                'Ice:Action1' => [
                    0 => [],
                    1 => [],
                ],
                'Ice:Action2' => [
                    'action2' => []
                ],
                'Ice:Action3' => [
                    0 => ['param1' => 'value1']
                ]
            ]
        );
//
////        $data = [
////            'Ice:Action4' => [
////                [],
////                ['param1' => 'value1'],
////                'action4-2' => ['param2' => 'value2'],
////                ['param3' => 'value3'],
////            ]
////        ];
////        try {
////            $actionContext->addAction($data);
////            $this->assertNotEquals(
////                $actionContext->getActions(),
////                [
////                    'Ice:Action1' => [
////                        0 => [],
////                        1 => [],
////                    ],
////                    'Ice:Action2' => [
////                        'action2' => []
////                    ],
////                    'Ice:Action3' => [
////                        0 => ['param1' => 'value1']
////                    ]
////                ]
////            );
////        } catch (Exception $e) {
////            $this->assertEquals(
////                $actionContext->getActions(),
////                [
////                    'Ice:Action1' => [
////                        0 => [],
////                        1 => [],
////                    ],
////                    'Ice:Action2' => [
////                        'action2' => []
////                    ],
////                    'Ice:Action3' => [
////                        0 => ['param1' => 'value1']
////                    ]
////                ]
////            );
////        }
//
//        $data = [
//            'Ice:Action5' => [
//                [],
//                ['param1' => 'value1'],
//                ['param2' => 'value2'],
//            ]
//        ];
//        $actionContext->addAction($data);
//
//        $this->assertEquals(
//            $actionContext->getActions(),
//            [
//                'Ice:Action1' => [
//                    0 => [],
//                    1 => [],
//                ],
//                'Ice:Action2' => [
//                    'action2' => []
//                ],
//                'Ice:Action3' => [
//                    0 => ['param1' => 'value1']
//                ],
//                'Ice:Action5' => [
//                    0 => [],
//                    1 => ['param1' => 'value1'],
//                    2 => ['param2' => 'value2'],
//                ]
//            ]
//        );
    }
}
