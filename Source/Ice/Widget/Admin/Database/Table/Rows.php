<?php

namespace Ice\Widget;

use Ice\Core\Config;
use Ice\Core\Debuger;
use Ice\Core\Model;
use Ice\Core\Module;
use Ice\Core\Query_Builder;
use Ice\Core\Security;
use Ice\Exception\Http_Forbidden;
use Ice\Exception\Http_Not_Found;

class Admin_Database_Table_Rows extends Table_Rows
{
    /**
     * Widget config
     *
     * @return array
     */
    protected static function config()
    {
        return [
            'render' => ['template' => Table_Rows::getClass(), 'class' => 'Ice:Php', 'layout' => null, 'resource' => null],
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => 'Widget: Access denied!'],
            'resource' => ['js' => null, 'css' => null, 'less' => null, 'img' => null],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'input' => [
                'schemeName' => ['providers' => 'router'],
                'tableName' => ['providers' => 'router']
            ],
            'output' => [],
        ];
    }

    /** Build widget
     *
     * @param array $input
     * @return array
     * @throws Http_Forbidden
     * @throws Http_Not_Found
     */
    protected function build(array $input)
    {
        $module = Module::getInstance();

        $currentDataSourceKey = $module->getDataSourceKeys()[$input['schemeName']];

        /** @var Model $modelClass */
        $modelClass = Module::getInstance()->getModelClass($input['tableName'], $currentDataSourceKey);

        $config = Config::getInstance(Admin_Database_Database::getClass());

        if (!isset($config->gets()[$currentDataSourceKey])) {
            throw new Http_Not_Found(['Scheme {$0} not found', $currentDataSourceKey]);
        }

        $scheme = Config::create($currentDataSourceKey, $config->gets()[$currentDataSourceKey]);

        $security = Security::getInstance();

        if (!$scheme->gets('roles', false) || !$security->check($scheme->gets('roles', false))) {
            throw new Http_Forbidden('Access denied: scheme not allowed');
        }

        $currentTableName = $modelClass::getTableName();

        if (!$scheme->gets('tables/' . $currentTableName)) {
            throw new Http_Not_Found(['Table {$0} not found', $currentTableName]);
        }

        $table = Config::create($currentTableName, $scheme->gets('tables/' . $currentTableName));

        if (!$table->gets('roles', false) || !$security->check($table->gets('roles', false))) {
            throw new Http_Forbidden('Access denied: table not allowed');
        }

        $this->setResource($modelClass);

        $this->a(
            $modelClass::getPkFieldName(),
            [
                'route' => 'ice_admin_database_row',
                'name' => 'pk',
                'title' => 'pk',
                'params' => $input
            ]
        );

        foreach ($table->gets('columns') as $columnName => $column) {
            $column = $table->getConfig('columns/' . $columnName);

            if (!$column->gets('showRoles', false)) {
                continue;
            }

            $this->span(
                $columnName,
                array_merge(['access' => ['roles' => $column->gets('showRoles', false)]], $column->gets('options', false))
            );
        }
    }
}