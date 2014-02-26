<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 15.01.14
 * Time: 0:56
 */

namespace ice\action;

use ice\core\Action;
use ice\core\action\Viewable;
use ice\core\Action_Context;
use ice\core\Model;
use ice\data\provider\Router;
use ice\view\render\Php;

class Html_Head_Title extends Action implements Viewable
{
    protected $layout = '';

    protected function init(Action_Context &$context)
    {
        parent::init($context);
        $context->setViewRenderClass(Php::VIEW_RENDER_PHP_CLASS);
        $context->addDataProviderKeys(Router::getDefaultKey());
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
            'title' => $input['route']['titleAction']
        );
    }
}