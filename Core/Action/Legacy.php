<?php
namespace ice\core\action;

use Controller_Manager;
use ice\core\Action;
use ice\core\Action_Context;

/**
 * Created by PhpStorm.
 * User: dp
 * Date: 10.12.13
 * Time: 12:21
 */
class Legacy extends Action
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
        $controllerAction = explode('/', $input['controllerAction']);
        unset($input['controllerAction']);

        $controllerTask = Controller_Manager::call($controllerAction[0], $controllerAction[1], $input);

        $output = $controllerTask->getTransaction()->buffer();
        $output['tempalate'] = $controllerTask->getTemplate();

        return $output;
    }

    protected function flush(Action_Context &$context)
    {
        parent::flush($context);
        $context->setTemplate($context->getData()['template']);
    }
}