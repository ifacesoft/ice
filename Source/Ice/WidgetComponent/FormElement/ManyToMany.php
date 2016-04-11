<?php

namespace Ice\WidgetComponent;

use Ice\Core\Widget as Core_Widget;

class FormElement_ManyToMany extends FormElement
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
        if (isset($part['options']['manyToMany'])) {
            /** @var Model $linkModelClass */
            /** @var Model $modelClass */
            list($modelClass, $linkFieldName, $linkModelClass, $fkFieldName, $linkFkFieldName) = $part['options']['manyToMany'];

            if (empty($values[$part['name']])) {
                $values[$part['name']] = 0;
            }
            if(array_key_exists('is_nested_sets', $part['options']) && $part['options']['is_nested_sets'] === true){
                $part['options']['rows'] = [0 => [$part['value'] => null, $modelClass::getFkFieldName() => null, $modelClass::getPkFieldName() => 0, $part['title'] => '', 'level' => 0]] +
                    $modelClass::createQueryBuilder()
                        ->left($linkModelClass, [$fkFieldName => $part['value']], $linkModelClass::getClassName() . '.' . $linkFieldName . '=' . $modelClass::getClassName() . '.' . $modelClass::getPkColumnName() . ' AND ' . $linkModelClass::getClassName() . '.' . $linkFkFieldName . '=' . $value)
                        ->group()
                        ->asc('left_key')
                        ->getSelectQuery([$part['title'], 'level'])
                        ->getRows();
                foreach($part['options']['rows'] as $key => $row){
                    $level = ($row['level'] - 1) * 4;
                    if($level < 0){
                        $level = 0;
                    }
                    $part['options']['rows'][$key]['category_name'] = str_repeat('&nbsp;', $level) . $row['category_name'];
                }
            } else {
                $part['options']['rows'] = [0 => [$part['value'] => null, $modelClass::getFkFieldName() => null, $modelClass::getPkFieldName() => 0, $part['title'] => '']] +
                    $modelClass::createQueryBuilder()
                        ->left($linkModelClass, [$fkFieldName => $part['value']], $linkModelClass::getClassName() . '.' . $linkFieldName . '=' . $modelClass::getClassName() . '.' . $modelClass::getPkColumnName() . ' AND ' . $linkModelClass::getClassName() . '.' . $linkFkFieldName . '=' . $value)
                        ->group()
                        ->getSelectQuery($part['title'])
                        ->getRows();

            }

            $manyToMany = array_filter($part['options']['rows'], function ($item) use ($value, $valueFieldName) {
                return $item[$valueFieldName] == $value;
            });

            $part['manyToMany'] = implode(', ', array_column($manyToMany, $part['title']));
            if(array_key_exists('is_nested_sets', $part['options']) && $part['options']['is_nested_sets'] === true){
                $part['manyToMany'] = str_replace('&nbsp;', '', $part['manyToMany']);
            }
        }
    }
}