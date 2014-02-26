<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 09.12.13
 * Time: 11:33
 */

namespace ice\core\action;

use ice\core\Action;
use ice\core\Action_Context;
use ice\core\helper\Object;
use ice\core\View;
use ice\data\provider\Request;
use ice\view\render\Php;

class Front_Ajax extends Action
{
    protected function init(Action_Context &$context)
    {
        parent::init($context);
        $context->setViewRenderClass(Php::VIEW_RENDER_PHP_CLASS);
        $context->addDataProviderKeys(Request::getDefaultKey());
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
        if (strpos($input['call'], '/')) {
            $input['params']['controllerAction'] = $input['call'];
            $input['call'] = 'Legacy';
        }

        $context->addAction($input['call'], $input['params']);

        return array(
            'frontAjax' => array(
                'Action' => Object::getName($input['call']),
            ),
            'back' => $input['back']
        );
    }

    protected function flush(Action_Context &$context)
    {
        /** @var View[] $data */
        $data = $context->getData();

        foreach ($data['frontAjax'] as &$action) {
            $action = array(
                'back' => $data['back'],
                'result' => array(
                    'data' => $data[$action][0]->getData(),
                    'html' => $data[$action][0]->render()
                )
            );
        }

        $context->setData($data);
    }
}