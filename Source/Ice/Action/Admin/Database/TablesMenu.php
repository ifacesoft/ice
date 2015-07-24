<?php

namespace Ice\Action;


use Ice\Core\Action;
use Ice\Core\Request;
use Ice\Widget\Menu\Nav;

class Admin_Database_TablesMenu extends Action {

    /**
     * Action config
     *
     * @return array
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
       protected static function config()
       {
           return [
               'view' => ['viewRenderClass' => 'Ice:Php'],
               'actions' => [],
               'input' => ['tables', 'tableName'],
               'output' => [],
               'cache' => ['ttl' => -1, 'count' => 1000],
               'roles' => []
           ];
       }

      /** Run action
     *
     * @param  array $input
     * @return array
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    public function run(array $input)
    {
        $navMenu = Nav::create(Request::uri(true), __CLASS__)
            ->setClasses('nav-pills nav-stacked');

        foreach ($input['tables'] as $table) {
            if ($input['tableName'] == $table['scheme']['tableName']) {
                $navMenu->link(
                    $table['scheme']['tableName'],
                    $table['scheme']['tableName'],
                    [
                        'converter' => ['Ice:Resource' => ['class' => 'modelClass']],
                        'tooltip' => $table['scheme']['comment'],
                        'active' => true
                    ]
                );
            } else {
                $navMenu->link(
                    $table['scheme']['tableName'],
                    $table['scheme']['tableName'],
                    [
                        'converter' => ['Ice:Resource' => ['class' => 'modelClass']],
                        'tooltip' => $table['scheme']['comment'],
                    ]
                );
            }
        }

        return ['navMenu' => $navMenu];
    }
}