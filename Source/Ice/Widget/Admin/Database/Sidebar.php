<?php

namespace Ice\Widget;

use Ice\Core\Data_Scheme;
use Ice\Core\Debuger;
use Ice\Core\Module;

class Admin_Database_Sidebar extends Nav
{
    /**
     * Widget config
     *
     * @return array
     */
    protected static function config()
    {
        return [
            'render' => ['template' => Nav::getClass(), 'class' => 'Ice:Php', 'layout' => null, 'resource' => null],
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => 'Widget: Access denied!'],
            'resource' => ['js' => null, 'css' => null, 'less' => null, 'img' => null],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'input' => ['routeParams' => ['providers' => 'router']],
            'output' => [],
            'action' => [
                //  'class' => 'Ice:Render',
                //  'params' => [
                //      'widgets' => [
                ////        'Widget_id' => Widget::class
                //      ]
                //  ],
                //  'method' => 'POST'
            ]
        ];
    }

    /** Build widget
     *
     * @param array $input
     * @return array
     */
    protected function build(array $input)
    {
        $module = Module::getInstance();

        $scheme = [];

        if (isset($input['routeParams']['dataSourceKey'])) {
            $currentDataSourceKey = $module->getDataSourceKeys()[$input['routeParams']['dataSourceKey']];

            foreach (Data_Scheme::getTables($module) as $dataSourceKey => $tables) {
                if ($dataSourceKey == $currentDataSourceKey) {
                    $scheme = $tables;
                    break;
                }
            }
            foreach ($scheme as $tableName => $table) {
                $this->li(
                    $tableName,
                    [
                        'route' => 'ice_admin_database_table',
                        'label' => $table['scheme']['comment'] . ' (' . $tableName . ')',
                        'params' => [
                            'dataSourceKey' => $input['routeParams']['dataSourceKey'],
                            'tableName' => $tableName
                        ]
                    ]
                );
            }
        }
    }
}