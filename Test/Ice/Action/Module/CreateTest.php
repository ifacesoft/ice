<?php
namespace Ice\Action;

use Ice\Core\Action_Context;
use PHPUnit_Framework_TestCase;

class Module_CreateTest extends PHPUnit_Framework_TestCase
{
    public function testActionRun()
    {
        Front_Cli::getInstance()->call(
            new Action_Context(),
            [
                'action' => 'Ice:Module_Create',
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