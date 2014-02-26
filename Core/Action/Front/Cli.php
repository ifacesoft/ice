<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 13.02.14
 * Time: 14:07
 */

namespace ice\core\action;

use ice\core\Action;
use ice\core\Action_Context;

class Front_Cli extends Action implements Cliable
{

    protected function init(Action_Context &$context)
    {
        parent::init($context);
        $context->addDataProviderKeys('Cli:prompt/');

        ini_set('memory_limit', '1024M');
    }

    /**
     * Запускает Экшин
     *
     * @param array $input
     * @param Action_Context $context
     * @return array
     */
    protected function run(array $input, Action_Context &$context)
    {
        $action = $input['action'];
        unset($input['action']);

        $context->addAction($action, $input);
    }
}