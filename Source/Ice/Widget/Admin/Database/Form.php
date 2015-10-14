<?php

namespace Ice\Widget;

use Ice\Action\Admin_Database;
use Ice\Core\Model;
use Ice\Core\Module;

class Admin_Database_Form extends Block
{
    /**
     * Widget config
     *
     * @return array
     */
    protected static function config()
    {
        return [
            'render' => ['template' => Block::getClass(), 'class' => 'Ice:Php', 'layout' => null, 'resource' => null],
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => 'Widget: Access denied!'],
            'resource' => ['js' => null, 'css' => null, 'less' => null, 'img' => null],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'input' => [
                'schemeName' => ['providers' => 'router', 'validators' => 'Ice:Not_Null'],
                'tableName' => ['providers' => 'router', 'validators' => 'Ice:Not_Empty'],
                'pk' => ['providers' => 'router', 'validators' => 'Ice:Not_Null'],
            ],
            'output' => [],
            'action' => [
                //  'class' => 'Ice:Render',
                //  'params' => [
                //      'widgets' => [
                ////        'Widget_id' => Widget::class
                //      ]
                //  ],
                //  'url' => true,
                //  'method' => 'POST',
                //  'callback' => null
            ]
        ];
    }

    protected function build(array $input)
    {
        $module = Module::getInstance();
        $currentDataSourceKey = $module->getDataSourceKeys()[$input['schemeName']];

        /** @var Model $modelClass */
        $modelClass = Module::getInstance()->getModelClass($input['tableName'], $currentDataSourceKey);

        $this->widget('form', ['widget' => [Admin_Database_Model_Form::class, ['pk' => $input['pk']], $modelClass]]);

//        $tableRows = Model_Table_Rows::getInstance($modelClass);
//        $tableRows->removePart($modelClass::getPkFieldName());
//        $tableRows->a(
//            'pk',
//            [
//                'route' => 'ice_admin_database_row',
//                'name' => $modelClass::getPkFieldName(),
//                'params' => [
//                    'dataSourceKey' => $input['dataSourceKey'],
//                    'tableName' => $input['modelClass']
//                ],
//                'unshift' => true
//            ]
//        );
//
//        $this
//            ->widget('trth', ['widget' => $tableRows], 'Ice\Widget\Table\Trth')
//            ->widget('rows', ['widget' => $tableRows])
//            ->widget('pagination', ['widget' => $this->getWidget(Pagination::getClass())]);
//
//        $modelClass::createQueryBuilder()
//            ->attachWidgets($this)
//            ->getSelectQuery('*')
//            ->getQueryResult();
//
//        $tableRows->render();
//
//        $this->setClasses('table-striped table-bordered table-hover table-condensed');
    }
}