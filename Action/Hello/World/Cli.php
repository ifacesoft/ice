<?php

namespace ice\action;

use ice\core\action\Cliable;
use ice\core\Action;
use ice\core\Action_Context;

/**
 * Created by PhpStorm.
 * User: dp
 * Date: 23.11.13
 * Time: 17:33
 */
class Hello_World_Cli extends Action implements Cliable
{
    /**
     * Запускает Экшин
     *
     * @param array $input
     * @param Action_Context $context
     * @return array
     */
    protected function run(array $input, Action_Context &$context)
    {
        echo 'Hello_World' . "\n";
    }
}