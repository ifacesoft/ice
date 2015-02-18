<?php
namespace Ice\Action;

use Ice\Core\Action_Context;
use Ice\Core\Logger;
use Ice\Data\Provider\Cli;
use PHPUnit_Framework_TestCase;

class Module_CreateTest extends PHPUnit_Framework_TestCase
{
    public function testActionRun()
    {
        $input = [
            'action' => 'Ice:Module_Create',
            'name' => 'MyProject',
            'alias' => 'Mp',
            'scheme' => 'test',
            'username' => 'root',
            'password' => '',
            'viewRender' => 'Php',
            'vcs' => 'git',
            'isWeb' => 'module'
        ];

        Cli::getInstance()->set($input);

        Module_Create::create($input)->call(Action_Context::create());
    }
}