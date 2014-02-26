<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 17.01.14
 * Time: 0:39
 */

namespace ice\action;

use ice\core\Action;
use ice\core\action\Viewable;
use ice\core\Action_Context;
use ice\view\render\Php;

class Http_Status_500 extends Action implements Viewable
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
        // TODO: Implement run() method.
    }
}