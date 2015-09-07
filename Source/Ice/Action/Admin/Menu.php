<?php

namespace Ice\Action;

use Ice\Core\Action;

class Admin_Menu extends Action
{

    /**
     * Action config
     *
     * @return array
     */
     protected static function config()
       {
           return [
               'view' => ['template' => '', 'viewRenderClass' => null, 'layout' => null],
               'actions' => [],
               'input' => [],
               'output' => [],
               'cache' => ['ttl' => -1, 'count' => 1000],
               'access' => ['roles' => [], 'request' => null, 'env' => null]
           ];
       }

      /** Run action
     *
     * @param  array $input
     * @return array
     */
    public function run(array $input)
    {
        // TODO: Implement run() method.
    }
}