<?php
namespace ice\core\action;

use ice\core\Action;
use ice\core\Action_Context;

/**
 * Created by PhpStorm.
 * User: dp
 * Date: 08.12.13
 * Time: 15:48
 */
class Layout extends Action
{
    protected $staticActions = array(
        'Html_Head_Title',
        'Html_Head_Resources'
    );

    protected $layout = '';

    /**
     * Запускает Экшин
     *
     * @param array $input
     * @param Action_Context $context
     * @return array
     */
    protected function run(array $input, Action_Context &$context)
    {
        if (isset($input['layoutTemplate'])) {
            $this->setTemplate($input['layoutTemplate']);
            unset($input['layoutTemplate']);
        }

        $action = $input['routeActions'];

        $params = array();

        if (strpos($action, '/')) {
            $params['controllerAction'] = $action;
            $action = 'Legacy';
        }

        $context->addAction($action, $params);

        return array(
            'layout' => array(
                'Action' => $action
            )
        );
    }

    protected function flush(Action_Context &$context)
    {
        $data = $context->getData();

        foreach ($data['layout'] as &$action) {
            $actionKey = $action;
            $action = $data[$action];
            unset($data[$actionKey]);
        }

        $context->setData($data);
    }
} 