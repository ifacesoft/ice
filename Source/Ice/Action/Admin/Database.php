<?php

namespace Ice\Action;

use Ice\Core\Action;
use Ice\Core\Data_Scheme;
use Ice\Core\Module;
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
            'output' => [],
            'ttl' => -1,
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
        $dataSourceKeysMenu = Nav::create()
            ->setClasses('nav-tabs');

        $module = Module::getInstance();

        $dataSourceKeys = $module->getDataSourceKeys();

        $dataSourceKey = isset($input['dataSourceKey'])
            ? $input['dataSourceKey']
            : reset($dataSourceKeys);

        foreach ($dataSourceKeys as $key) {
            if ($dataSourceKey == $key) {
                $dataSourceKeysMenu->item($key, $key, ['active' => true]);
            } else {
                $dataSourceKeysMenu->item($key, $key);
            }
        }

        $tables = Data_Scheme::getTables($module);

        $tables = isset($tables[$dataSourceKey])
            ? $tables[$dataSourceKey]
            : [];

        $tableName = isset($input['tableName'])
            ? $input['tableName']
            : reset($tables)['scheme']['tableName'];

        $tablesMenu = Nav::create();

        foreach ($tables as $table) {
            if ($tableName == $table['scheme']['tableName']) {
                $tablesMenu->link(
                    $table['scheme']['tableName'],
                    'Таблица',
                    [
                        'converter' => ['Ice:Resource' => ['class' => 'modelClass']],
                        'tooltip' => $table['scheme']['comment']
                    ]
                );
            } else {
                $tablesMenu->link(
                    $table['scheme']['tableName'],
                    'Таблица',
                    [
                        'converter' => ['Ice:Resource' => ['class' => 'modelClass']],
                        'tooltip' => $table['scheme']['comment'],
                        'active' => true
                    ]
                );
            }
        }

        $this->addAction(['Ice:Crud', ['modelClass' => $tables[$tableName]['modelClass']]]);

        return [
            'dataSourceKeysMenu' => $dataSourceKeysMenu,
            'tablesMenu' => $tablesMenu
        ];
    }
}
