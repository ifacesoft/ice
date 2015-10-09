<?php

namespace Ice\Action;

use Ice\Core\Model;
use Ice\Core\Widget;
use Ice\Widget\Form_Model;

class Form_Model_Save extends Widget
{
    /**
     * Widget config
     *
     * @return array
     */
    protected static function config()
    {
        return [
            'render' => ['template' => null, 'class' => 'Ice:Php', 'layout' => null, 'resource' => null],
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => 'Widget: Access denied!'],
            'resource' => ['js' => null, 'css' => null, 'less' => null, 'img' => null],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'input' => ['form' => ['validators' => 'Ice:Not_Empty'],],
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
        /** @var Form_Model $form */
        $form = $input['form'];

        /** @var Model $modelClass */
        $modelClass = $form->getModelClass();
        $modelClass::create($form->validate())->save(true);
    }
}