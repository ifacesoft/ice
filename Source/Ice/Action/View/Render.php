<?php

namespace Ice\Action;


use Ice\Core\Action;
use Ice\Core\Debuger;
use Ice\Core\View;

class View_Render extends Action
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
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => 'Action: Access denied!'],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'actions' => [],
            'input' => ['viewClass' => ['validators' => 'Ice:Not_Empty']],
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
        /** @var View $viewClass */
        $viewClass = View::getClass($input['viewClass']);

        return ['content' => $viewClass::getInstance()->render()];
    }
}