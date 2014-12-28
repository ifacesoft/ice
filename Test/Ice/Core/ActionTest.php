<?php

namespace Ice\Core;

use Ice\Action\Test;
use PHPUnit_Framework_TestCase;

class ActionTest extends PHPUnit_Framework_TestCase
{
    public function testActionRun()
    {
        $actionContext = new Action_Context();

        /** @var View $view */
        $view = Test::getInstance()->call($actionContext, ['test' => 'ok']);

        $this->assertEquals($view->getContent(), '
inputTestPhp1
testPhpOk
inputTestSmarty

testSmartyOk

inputTestTwig
testTwigOk
inputTestPhp2
testPhpOk');
    }
}
