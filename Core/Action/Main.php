<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 10.01.14
 * Time: 23:04
 */

namespace ice\core\action;

use ice\core\Action;
use ice\core\Action_Context;
use ice\view\render\Php;

class Main extends Action
{

    protected function init(Action_Context &$context)
    {
        parent::init($context);
        $context->setViewRenderClass(Php::VIEW_RENDER_PHP_CLASS);
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
        return array(
            'welcome' => 'Hello world',
            'test' => 'test'
        );
    }
}