<?php

namespace Ice\Widget;

use Ice\Core\Debuger;
use Ice\Core\Model;

class Model_Table extends Table
{
    public static function schemeColumnPlugin($columnName, $table)
    {
        return 'text';
    }

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
            'input' => ['config' => ['validators' => 'Ice:Not_Empty']],
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

    /** Build widget
     *
     * @param array $input
     * @return array
     */
    protected function build(array $input)
    {
        /** @var Model $modelClass */
        $modelClass = $this->getInstanceKey();

        $this->setResource($modelClass);

        $tableRows = Model_Table_Rows::getInstance($modelClass, null, ['config' => $input['config']]);

        $this
            ->widget('trth', ['widget' => $tableRows], 'Ice\Widget\Table\Trth')
            ->widget('rows', ['widget' => $tableRows])
            ->widget('pagination', ['widget' => $this->getWidget(Pagination::getClass())]);

        $modelClass::createQueryBuilder()
            ->attachWidgets($this)
            ->getSelectQuery('*')
            ->getQueryResult();
    }
}