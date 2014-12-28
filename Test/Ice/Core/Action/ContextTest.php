<?php
namespace Ice\Core;

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

        $data = [
            'Ice:Action5' => [
                ['param1' => 'value1'],
                ['param2' => 'value2'],
            ]
        ];
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
                ],
                'Ice:Action5' => [
                    0 => ['param1' => 'value1'],
                    1 => ['param2' => 'value2'],
                ]
            ]
        );

//        $data = [
//            'title' => ['Ice:Title' => ['title' => 'Hello world']]
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
//                    0 => ['param1' => 'value1'],
//                    1 => ['param2' => 'value2'],
//                ],
//                'Ice:Title' => [
//                    'title' => ['title' => 'Hello world']
//                ]
//            ]
//        );
    }
}
