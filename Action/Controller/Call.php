<?php

namespace ice\action;

use Controller_Manager;
use ice\core\action\Cliable;
use ice\core\Action;
use ice\core\Action_Context;

/**
 * Created by PhpStorm.
 * User: dp
 * Date: 24.11.13
 * Time: 17:55
 */
class Controller_Call extends Action implements Cliable
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
        $controllerAction = explode('/', $input['name']);
        unset($input['name']);

        Controller_Manager::call($controllerAction[0], $controllerAction[1], $input);
    }
}