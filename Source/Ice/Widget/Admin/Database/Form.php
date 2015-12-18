<?php

namespace Ice\Widget;

use Ice\Action\Admin_Database;
use Ice\Core\Config;
use Ice\Core\Data_Scheme;
use Ice\Core\Debuger;
use Ice\Core\Model;
use Ice\Core\Module;
use Ice\Core\Security;
use Ice\Core\Validator;
use Ice\Exception\Http_Forbidden;
use Ice\Exception\Http_Not_Found;

class Admin_Database_Form extends Form
{
    /**
     * Widget config
     *
     * @return array
     */
    protected static function config()
    {
        return [
            'render' => ['template' => Form::getClass(), 'class' => 'Ice:Php', 'layout' => null, 'resource' => Admin_Database_Database::getClass()],
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => 'Widget: Access denied!'],
            'resource' => ['js' => null, 'css' => null, 'less' => null, 'img' => null],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'input' => [
                'schemeName' => ['providers' => 'router', 'validators' => 'Ice:Not_Null'],
                'tableName' => ['providers' => 'router', 'validators' => 'Ice:Not_Empty'],
                'pk' => ['providers' => 'router'],
                'mode' => ['provider' => 'default', 'default' => 'create']
            ],
            'output' => [],
        ];
    }

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

        $this->setResource($modelClass);

        $currentTableName = $modelClass::getTableName();

        if (!$scheme->gets('tables/' . $currentTableName)) {
            throw new Http_Not_Found(['Table {$0} not found', $currentTableName]);
        }

        $table = Config::create($currentTableName, $scheme->gets('tables/' . $currentTableName));

        if (!$table->gets('roles', false) || !$security->check($table->gets('roles', false))) {
            throw new Http_Forbidden('Access denied: table not allowed');
        }

        if ($input['mode'] != 'filter') {
            $this->setHorizontal();
        }

        if ($input['mode'] == 'edit') {
            $this->text($modelClass::getPkFieldName(), ['readonly' => true]);
        }

        $columns = Data_Scheme::getTables(Module::getInstance())[$currentDataSourceKey][$currentTableName]['columns'];

        foreach ($table->gets('columns') as $columnName => $column) {
            $column = $table->getConfig('columns/' . $columnName);

            if ($roles = $column->gets($input['mode'] . 'Roles', false)) {
                $options = $column->gets('options', false);

                $options['access'] = ['roles' => $roles];

                if (isset($columns[$columnName])) {
                    $fieldType = $column->get('type', false) ? $column->get('type') : $columns[$columnName][Model_Form::getClass()]['type'];

                    $options['validators'] = $columns[$columnName][Validator::getClass()];
                    $options['placeholder'] = $columns[$columnName]['fieldName'] . '_placeholder';

                    $this->$fieldType($columns[$columnName]['fieldName'], $options);

                    continue;
                }

                $options['placeholder'] = $columnName . '_placeholder';

                if ($column->get('options/oneToMany', false)) {
                    $this->combobox($columnName, $options);
                } else if ($column->get('options/manyToMany', false)) {
                    $options['multiple'] = true;

                    $this->combobox($columnName, $options);
                } else {
                    $this->text($columnName, $options);
                }
            }
        }

        $this->div('ice-message', ['label' => '&nbsp;']);

        switch ($input['mode']) {
            case 'filter':
                $this->button('filter', ['onclick' => ['action' => 'Ice:Render', 'method' => 'GET'], 'classes' => 'btn-default', 'params' =>['widgets' => ['admin_database_roll' => 'Ice:Admin_Database_Table']]]);
                break;
            case 'edit':
                $this->button('edit', ['submit' => ['action' => '_Submit'], 'classes' => 'btn-primary']);
                break;
            case 'create':
                $this->button('save', ['submit' => ['action' => '_Submit'], 'classes' => 'btn-success']);
                break;
            default:
                break;
        }


        if ($input['pk']) {
            $this->bind($modelClass::getModel($input['pk'], '*')->get());
        }
    }
}