<?php
namespace Ice\Action;

use Ice\Core\Action;
use Ice\Core\Debuger;
use Ice\Core\View;

class Layout_Admin extends Action
{
    /**
     * Action config
     *
     * @return array
     */
    protected static function config()
    {
        return [
            'view' => ['template' => null, 'viewRenderClass' => 'Ice:Php', 'layout' => ''],
            'access' => ['roles' => 'ROLE_ICE_ADMIN', 'request' => null, 'env' => null, 'message' => 'Action: Access denied!'],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'actions' => [
                'Ice:Admin_Navigation',
                'Ice:Admin_Sidebar',
                'Ice:Resource_Css',
                'Ice:Resource_Js',
                'Ice:Resource_Dynamic'
            ],
            'input' => ['views' => ['default' => []]],
            'output' => []
        ];
    }

    /** Run action
     *
     * @param  array $input
     * @return array
     */
    public function run(array $input)
    {
        array_walk(
            $input['views'],
            function (&$view) {
                /** @var View $viewClass */
                $viewClass = View::getClass($view);
                /** @var View $view */
                $view = $viewClass::getInstance();
                $view = $view->render();
            }
        );

        return $input['views'];
    }
}
