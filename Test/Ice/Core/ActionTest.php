<?php

namespace Ice\Core;

use Ice\Action\Test;
use Ice\Data\Provider\Router;
use PHPUnit_Framework_TestCase;

class ActionTest extends PHPUnit_Framework_TestCase
{
    public function testGetActions()
    {
        $action = Test::create();

        $input = [
            0 => [''],

            1 => [
                'Ice:Test'
            ],
            2 => [
                ['Ice:Test']
            ],
            3 => [
                ['Ice:Test' => 'key']
            ],
            4 => [
                ['Ice:Test', ['test' => 'test4']]
            ],
            5 => [
                ['_My' => 'key', ['test' => 'test5']]
            ],
            6 => [
                ['Ice:Title' => 'title', ['title' => 'Hello world']],
                '_Test',
                'Ice:Main' => 'main'
            ]
        ];

        $output = [
            0 => [],
            1 => [
                0 => [
                    'Ice\Action\Test' => []
                ]
            ],
            2 => [
                0 => [
                    'Ice\Action\Test' => []
                ]
            ],
            3 => [
                'key' => [
                    'Ice\Action\Test' => []
                ]
            ],
            4 => [
                0 => [
                    'Ice\Action\Test' => [
                        'test' => 'test4'
                    ]
                ]
            ],
            5 => [
                'key' => [
                    'Ice\Action\Test_My' => [
                        'test' => 'test5'
                    ]
                ]
            ],
            6 => [
                'title' => [
                    'Ice\Action\Title' => [
                        'title' => 'Hello world'
                    ]
                ],
                0 => [
                    'Ice\Action\Test_Test' => []
                ],
                'main' => [
                    'Ice\Action\Main' => []
                ]
            ]
        ];

        foreach ($input as $key => $actions) {
            $this->assertEquals($action->getActions($actions), $output[$key]);
        }
    }


    public function testActionRun()
    {
        $_SERVER['REQUEST_URI'] = '/test';
        $router = Router::getInstance();
        $route = Route::getInstance($router->get('routeName'));
        $method = $route->gets('request/' . $router->get('method'));

        /** @var Action $actionClass */
        list($actionClass, $input) = each($method);

        $this->assertEquals($actionClass::call($input)->getContent(), 'Layout Test

inputTestPhp1
testPhpOk
inputTestSmarty

testSmartyOk

inputTestTwig

testTwigOk

inputTestPhp2
testPhpOk
test');
    }


}
