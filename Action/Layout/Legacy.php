<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 10.12.13
 * Time: 18:34
 */

namespace ice\action;


use ice\core\action\Legacy;
use ice\core\Action_Context;
use ice\core\View;

class Layout_Legacy extends Legacy
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
        if (isset($input['layoutTemplate'])) {
            $this->setTemplate($input['layoutTemplate']);
            unset($input['layoutTemplate']);
        }

        $params = reset(parent::run($input, $context)['tasks'])->getTransaction()->buffer();
        $action = $input['routeActions'];

        if (strpos($action, '/')) {
            $params['controllerAction'] = $action;
            $action = 'Legacy';
            if (isset($input['action'])) {
                $params['action'] = $input['action'];
            }
        }

        $context->addAction($action, $params);

        return array(
            'content' => $action,
            'routeTitle' => $input['routeTitle']
        );
    }

    protected function flush(Action_Context &$context)
    {
        /** @var View[] $data */
        $data = $context->getData();

        $data['content'] = $data[$data['content']][0]->render();

        $context->setData($data);
    }
} 