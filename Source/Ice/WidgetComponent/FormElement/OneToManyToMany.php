<?php

namespace Ice\WidgetComponent;

class FormElement_OneToManyToMany extends FormElement
{
    /**
     * WidgetComponent config
     *
     * @return array
     */
    protected static function config()
    {
        return [
            'render' => ['template' => true, 'class' => 'Ice:Php', 'layout' => null, 'resource' => null],
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => 'WidgetComponent: Access denied!'],
            'cache' => ['ttl' => -1, 'count' => 1000],
        ];
    }

    public function initValues() {
        if (isset($part['options']['oneToManyToMany'])) {
            /** @var Model $linkModelClass1 */
            /** @var Model $linkModelClass2 */
            list($linkModelClass1, $linkFieldName, $linkModelClass2) = $part['options']['oneToManyToMany'];
            $model = $linkModelClass1::getModel($values[$part['name']], $linkFieldName);
            $model = $model ? $model->fetchOne($linkModelClass2, $part['value'], true) : null;
            $values[$part['value']] = $model ? $model->get($part['value'], false) : '&nbsp;';
        }
    }
}