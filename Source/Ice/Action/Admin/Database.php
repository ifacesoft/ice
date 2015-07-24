<?php

namespace Ice\Action;

use Ice\Core\Action;
use Ice\Core\Data_Scheme;
use Ice\Core\Module;
use Ice\Core\Request;
use Ice\Helper\Emmet;
use Ice\Widget\Menu\Nav;

class Admin_Database extends Action
{
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
            'input' => [],
            'output' => ['resource' => 'Ice:Resource/Ice\Action\Admin_Database'],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'roles' => []
        ];
    }

    /**
     * Run action
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
        $dataSourceKeysMenu = Nav::create(Request::uri(true), __CLASS__)
            ->setClasses('nav-tabs');

        $module = Module::getInstance();

        $dataSourceKeys = $module->getDataSourceKeys();

        $dataSourceKey = isset($input['dataSourceKey'])
            ? $input['dataSourceKey']
            : reset($dataSourceKeys);

        foreach ($dataSourceKeys as $key) {
            if ($dataSourceKey == $key) {
                $dataSourceKeysMenu->link($key, $key, ['active' => true]);
            } else {
                $dataSourceKeysMenu->link($key, $key);
            }
        }

        $tables = Data_Scheme::getTables($module);

        $tables = isset($tables[$dataSourceKey])
            ? $tables[$dataSourceKey]
            : [];

        $tableName = isset($input['tableName'])
            ? $input['tableName']
            : reset($tables)['scheme']['tableName'];

        $this->addAction([
            'Ice:Admin_Database_TablesMenu',
            [
                'tables' => $tables,
                'tableName' => $tableName,
                'layout' => Emmet::PANEL_BODY
            ]
        ]);

        if (!empty($tables)) {
            $this->addAction(['Ice:Crud', ['modelClassName' => $tables[$tableName]['modelClass']]]);
        }

        return [
            'dataSourceKeysMenu' => $dataSourceKeysMenu,
        ];
    }
}
