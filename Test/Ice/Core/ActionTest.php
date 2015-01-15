<?php

namespace Ice\Core;

use Ice\Action\Front;
use PHPUnit_Framework_TestCase;

class ActionTest extends PHPUnit_Framework_TestCase
{
    public function testActionRun()
    {
        $_SERVER['REQUEST_URI'] = '/ice/test';

        $actionContext = new Action_Context();

        /** @var View $view */
        $view = Front::getInstance()->call($actionContext);

        $this->assertEquals($view->getContent(), 'Layout Test

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
