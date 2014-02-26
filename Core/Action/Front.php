<?php
namespace ice\core\action;

use ice\core\Action;
use ice\core\Action_Context;
use ice\core\Model;
use ice\data\provider\Router;
use ice\view\render\Php;

/**
 * Created by PhpStorm.
 * User: dp
 * Date: 08.12.13
 * Time: 12:53
 */
class Front extends Action
{
    const LEGACY_CONTENT = 'content';

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
        $route = $input['route'];

        $layoutParams = array(
            'layoutTemplate' => $route['layoutTemplate'],
            'routeActions' => $route['actions'],
            'routeTitle' => $route['titleAction']
        );

        if (strpos($route['layoutAction'], '/')) {
            $layoutParams['controllerAction'] = $route['layoutAction'];
            $route['layoutAction'] = 'Layout_Legacy';
            if (isset($input['action'])) {
                $layoutParams['action'] = $input['action'];
            }
        }

        $context->addAction($route['layoutAction'], $layoutParams);

        return array(
            'front' => array(
                'Action_Layout' => $route['layoutAction']
            )
        );
    }

    protected function flush(Action_Context &$context)
    {
        $data = $context->getData();

        foreach ($data['front'] as &$layout) {
            $layoutKey = $layout;
            $layout = $data[$layout];
            unset($data[$layoutKey]);
        }

        $context->setData($data);
    }
}