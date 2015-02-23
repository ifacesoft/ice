<?php
namespace Ice\Action;

use Ice;
use Ice\Core\Action;
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

        /** @var Action $actionClass */
        $actionClass = Module_Create::getClass();

        $actionClass::call($input);
    }
}