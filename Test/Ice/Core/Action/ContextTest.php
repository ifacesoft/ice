<?php
namespace Ice\Core\Action;

use Ice\Core\Action_Context;
use PHPUnit_Framework_TestCase;

class Action_ContextTest extends PHPUnit_Framework_TestCase
{
    public function testAddAction()
    {
        $actionContext = new Action_Context();
        $data = 'Ice:Action1';
        $actionContext->addAction($data);

        $this->assertEquals(
            $actionContext->getActions(),
            [
                'Ice:Action1' => [
                    0 => [],
                ]
            ]
        );

        $data = 'Ice:Action1';
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
                    0 => [
                        'param1' => 'value1'
                    ]
                ]
            ]
        );

//        $data = [
//            'Ice:Action4' => [
//                ['param1' => 'value1'],
//                'action4-2' => ['param2' => 'value2'],
//                ['param3' => 'value3'],
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
//                    0 => [
//                        'param1' => 'value1'
//                    ]
//                ],
//                'Ice:Action4' => [
//                    0 => [
//                        'param1' => 'value1'
//                    ],
//                    'action4-2' => [
//                        'param2' => 'value2'
//                    ],
//                    1 => [
//                        'param3' => 'value3'
//                    ]
//                ]
//            ]
//        );
    }
}
