<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 09.10.15
 * Time: 18:25
 */

namespace Ice\Widget;


use Ice\Core\Model;

class Model_Table_Rows extends Table_Rows
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
            'input' => [],
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

        foreach ($modelClass::getScheme()->getColumnFieldMap() as $fieldName) {
            $this->span($fieldName);
        }
    }
}