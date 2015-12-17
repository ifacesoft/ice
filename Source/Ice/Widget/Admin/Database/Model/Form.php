<?php

namespace Ice\Widget;

use Ice\Core\Config;
use Ice\Core\Data_Scheme;
use Ice\Core\Model;
use Ice\Core\Module;
use Ice\Core\Security;
use Ice\Core\Validator;
use Ice\Core\Widget;
use Ice\Exception\Http_Forbidden;
use Ice\Exception\Http_Not_Found;
use Ice\Exception\Not_Configured;

class Admin_Database_Model_Form extends Model_Form
{
    /**
     * Widget config
     *
     * @return array
     */
    protected static function config()
    {
        return [
            'render' => ['template' => Form::getClass(), 'class' => 'Ice:Php', 'layout' => null, 'resource' => null],
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => 'Widget: Access denied!'],
            'resource' => ['js' => null, 'css' => null, 'less' => null, 'img' => null],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'input' => ['pk' => ['validators' => 'Ice:Not_Null', 'providers' => 'any']],
            'output' => [],
        ];
    }

    /** Build widget
     *
     * @param array $input
     * @return array
     * @throws Http_Forbidden
     * @throws Not_Configured
     */
    protected function build(array $input)
    {




//        $this->setResource($modelClass);
//
//        $pkFieldName = $modelClass::getPkFieldName();
//
//        $currentTableName = $modelClass::getTableName();
//
//        $currentDataSourceKey = $modelClass::getDataSourceKey();
//
//        $scheme = Data_Scheme::getTables(Module::getInstance())[$currentDataSourceKey][$currentTableName];
//
//        $fieldNames = [];
//
//        foreach ($scheme['columns'] as $column) {
//            if (!array_key_exists($column['fieldName'], $models[$modelClass]['fields'])) {
//                continue;
//            }
//
//            $field = $models[$modelClass]['fields'][$column['fieldName']];
//
//            $options = array_merge(
//                [
//                    'label' => $column['scheme']['comment'] . ' (' . $column['fieldName'] . ')',
//                    'validators' => $column[Validator::getClass()],
//                    'readonly' => $column['fieldName'] == $pkFieldName, // 'readonly' => in_array($fieldName, $pkFieldNames)
//                ],
//                $field
//            );
//
//            $fieldType = isset($field['type']) ? $field['type'] : $column[Model_Form::getClass()]['type'];
//
//            $this->$fieldType($column['fieldName'], $options);
//
//            $fieldNames[] = $column['fieldName'];
//        }
//
//        if (empty($fieldNames)) {
//            throw new Http_Forbidden(['Access forbidden or all fields of model {$0} on {$1}', [$modelClass, get_class($this)]]);
//        }
//
//        $this->div('ice-message', ['label' => '&nbsp;']);
//
//        foreach ($models[$modelClass]['actions'] as $name => $options) {
//            $this->button($name, $options);
//        }
//
//        if ($input['pk']) {
//            $this->bind($modelClass::getModel($input['pk'], $fieldNames)->get());
//        }
    }
}