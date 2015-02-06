<?php
namespace Ice\Action;

use Ice\Core\Action_Context;
use PHPUnit_Framework_TestCase;

class Module_CreateTest extends PHPUnit_Framework_TestCase
{
    public function testActionRun()
    {
        Module_Create::getInstance()->call(
            Action_Context::create(),
            [
                'name' => 'MyProject',
                'alias' => 'Mp',
                'scheme' => 'test',
                'username' => 'root',
                'password' => '',
                'viewRender' => 'Php',
                'vcs' => 'git',
                'isWeb' => 'module'
            ]
        );
    }
}