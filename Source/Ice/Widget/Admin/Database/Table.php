<?php

namespace Ice\Widget;

use Ice\Action\Render;
use Ice\Core\Config;
use Ice\Core\Data_Scheme;
use Ice\Core\Debuger;
use Ice\Core\Model;
use Ice\Core\Module;
use Ice\Core\Query_Builder;
use Ice\Core\Request;
use Ice\Core\Security;

class Admin_Database_Table extends Table
{
    /**
     * Widget config
     *
     * @return array
     */
    protected static function config()
    {
        return [
            'render' => ['template' => Table::getClass(), 'class' => 'Ice:Php', 'layout' => null, 'resource' => null],
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => 'Widget: Access denied!'],
            'resource' => ['js' => null, 'css' => null, 'less' => null, 'img' => null],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'input' => [
                'schemeName' => ['providers' => 'router', 'validators' => 'Ice:Not_Null'],
                'tableName' => ['providers' => 'router', 'validators' => 'Ice:Not_Empty']
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

        $this->setResource($modelClass);

        $tableRows = $this->getWidget(['_Rows', [], $modelClass]);

        /** @var Pagination $pagination */
        $pagination = $this->getWidget(Pagination::getClass())
            ->bind(['limit' => 10])
            ->setEvent([
                'action' => Render::class,
                'data' => ['widgets' => ['admin_database_roll' => __CLASS__]]
            ]);

        $this
            ->a(
                'add',
                [
                    'route' => 'ice_admin_database_row_create',
                    'params' => [
                        'schemeName' => $input['schemeName'],
                        'tableName' => $input['tableName']
                    ],
                    'classes' => 'btn btn-success',
                    'resource' => '/Ice/Widget/Admin_Database_Database'
                ]
            )
            ->widget('trth', ['widget' => $tableRows], 'Ice\Widget\Table\Trth')
            ->widget('rows', ['widget' => $tableRows])
            ->widget('pagination', ['widget' => $pagination]);

        ini_set('memory_limit', '4G');
        ini_set('max_execution_time', 120);

        $modelClass::createQueryBuilder()
            ->attachWidgets($this)
            ->getSelectQuery('*')->dump(); die();

        $modelClass::createQueryBuilder()
            ->attachWidgets($this)
            ->getSelectQuery('*')
            ->getQueryResult();
    }

    public function queryBuilderPart(Query_Builder $queryBuilder, array $input)
    {
        parent::queryBuilderPart($queryBuilder, $input);
        $module = Module::getInstance();

        $currentDataSourceKey = $module->getDataSourceKeys()[$input['schemeName']];

        /** @var Model $modelClass */
        $modelClass = Module::getInstance()->getModelClass($input['tableName'], $currentDataSourceKey);

        $config = Config::getInstance(Admin_Database_Database::getClass());

        $scheme = Config::create($currentDataSourceKey, $config->gets()[$currentDataSourceKey]);

        $currentTableName = $modelClass::getTableName();

        $table = Config::create($currentTableName, $scheme->gets('tables/' . $currentTableName));

        $security = Security::getInstance();

        $columnFieldMap = $modelClass::getScheme()->getColumnFieldMap();

        $params = Request::getParams();

        $columns = Data_Scheme::getTables(Module::getInstance())[$currentDataSourceKey][$currentTableName]['columns'];

        foreach ($table->gets('columns') as $columnName => $column) {
            $column = $table->getConfig('columns/' . $columnName);

            if (!$column->gets('filterRoles', false) || !$security->check($column->gets('filterRoles', false))) {
                continue;
            }

            if (isset($columns[$columnName])) {
                $fieldName = $columnFieldMap[$columnName];

                if (isset($params[$fieldName]) && $params[$fieldName] !== '') {
//                    if (is_numeric($params[$fieldName])) {
//                        $queryBuilder->eq([$fieldName => $params[$fieldName]]);
//                    } else {
                        $queryBuilder->like($fieldName, '%' . $params[$fieldName] . '%');
//                    }
                }

                continue;
            }

            if ($column->get('options/oneToMany', false)) {
                $fieldName = $column->get('options/name', false);

                if (!$fieldName) {
                    $fieldName = $columnName;
                }

                if (isset($params[$fieldName]) && $params[$fieldName] !== '0') {
                    $queryBuilder->eq([$fieldName => $params[$fieldName]]);
                }
            } else if ($column->get('options/manyToMany', false)) {
                $fieldName = $column->get('options/name', false);

                if (!$fieldName) {
                    $fieldName = $columnName;
                }

                /**
                 * @var Model $manyModelClass
                 * @var Model $linkModelClass
                 */
                foreach ($modelClass::getScheme()->gets('relations/manyToMany') as $manyModelClass => $linkModelClass) {
                    if ($manyModelClass::getPkFieldName() == $fieldName) {
                        $values = empty($params[$fieldName]) ? [] : array_filter($params[$fieldName]);

                        if (!empty($values)) {
                            $queryBuilder
                                ->inner($manyModelClass, '/pk')
                                ->in('/pk', $values, $manyModelClass);
                        }

                        break;
                    }
                }

            } else {
//                $this->eq([$columnName => $params[$columnName]]);
            }
        }
    }
}