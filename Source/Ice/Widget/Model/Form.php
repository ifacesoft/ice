<?php
/**
 * Ice form implementation model class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Widget;

use Ice\Core\Data_Scheme;
use Ice\Core\Model as Core_Model;
use Ice\Core\Model;
use Ice\Core\Module;
use Ice\Core\Validator;
use Ice\Core\Widget;
use Ice\Exception\Http_Forbidden;
use Ice\Exception\Not_Configured;

/**
 * Class Model
 *
 * Binds forms and submit data for model objects
 *
 * @see Ice\Widget\Form
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Widget
 */
abstract class Model_Form extends Form
{
    /**
     * Field type map
     *
     * @var array
     */
    public static $typeMap = [
        'int' => 'number',
        'varchar' => 'text',
        'datetime' => 'date',
        'timestamp' => 'date',
        'tinyint' => 'checkbox',
        'point' => 'map',
        'bigint' => 'number',
        'text' => 'textarea',
        'double' => 'text',
        'longtext' => 'textarea'
    ];

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
        /** @var Model $modelClass */
        $modelClass = $this->getInstanceKey();

        /** @var Widget $widgetClass */
        $widgetClass = get_class($this);

        try {
            $models = $widgetClass::getConfig()->gets('models');
        } catch (\Exception $e) {
            throw new Not_Configured(['Check config of widget {$0} for {$1}', [get_class($this), $modelClass]], [], $e);
        }

        $this->setResource($modelClass);

        $pkFieldName = $modelClass::getPkFieldName();

        $currentTableName = $modelClass::getTableName();

        $currentDataSourceKey = $modelClass::getDataSourceKey();

        $scheme = Data_Scheme::getTables(Module::getInstance())[$currentDataSourceKey][$currentTableName];

        $fieldNames = [];

        foreach ($scheme['columns'] as $column) {
            if (!array_key_exists($column['fieldName'], $models[$modelClass]['fields'])) {
                continue;
            }

            $field = $models[$modelClass]['fields'][$column['fieldName']];

            $options = array_merge(
                [
                    'label' => $column['scheme']['comment'] . ' (' . $column['fieldName'] . ')',
                    'validators' => $column[Validator::getClass()],
                    'readonly' => $column['fieldName'] == $pkFieldName, // 'readonly' => in_array($fieldName, $pkFieldNames)
                ],
                $field
            );

            $fieldType = isset($field['type']) ? $field['type'] : $column[Model_Form::getClass()]['type'];

            $this->$fieldType($column['fieldName'], $options);

            $fieldNames[] = $column['fieldName'];
        }

        if (empty($fieldNames)) {
            throw new Http_Forbidden(['Access forbidden or all fields of model {$0} on {$1}', [$modelClass, get_class($this)]]);
        }

        $this->div('ice-message', ['label' => '&nbsp;']);

        foreach ($models[$modelClass]['actions'] as $name => $options) {
            $this->button($name, $options);
        }

        if ($input['pk']) {
            $this->bind($modelClass::getModel($input['pk'], $fieldNames)->get());
        }
    }

//    /**
//     * Constructor for model forms
//     *
//     * @author dp <denis.a.shestakov@gmail.com>
//     *
//     * @version 0.2
//     * @since   0.0
//     */
//    protected function __construct()
//    {
//        /**
//         * @var Core_Model $modelClass
//         */
//        $modelClass = $this->getValues();
//
//        $validateScheme = $modelClass::getPlugin(Validator::getClass());
//
//        $pkFieldNames = $modelClass::getScheme()->getPkFieldNames();
//
//        foreach ($modelClass::getPlugin(__CLASS__) as $fieldName => $fieldType) {
//            $this->$fieldType(
//                $fieldName,
//                $modelClass::getFieldTitle($fieldName),
//                [
//                    'placeholder' => $modelClass::getFieldPlaceholder($fieldName),
//                    'readonly' => in_array($fieldName, $pkFieldNames)
//                ],
//                $validateScheme[$fieldName]
//            );
//        }
//    }

//    /**
//     * Binds all model field values
//     *
//     * @param Core_Model $model
//     * @return Model_Form
//     *
//     * @author dp <denis.a.shestakov@gmail.com>
//     *
//     * @version 0.0
//     * @since 0.0
//     */
//    public function bindModel(Core_Model $model)
//    {
//        return $this->bind(array_merge($model->get(), $model->getPk()));
//    }

    public function addFilterFields(array $filterFields)
    {
        /**
         * @var Core_Model $modelClass
         */
        $modelClass = $this->getValues();

        foreach ($filterFields as &$filterField) {
            $filterField = $modelClass::getFieldName($filterField);
        }

        return parent::addFilterFields($filterFields);
    }

    public static function schemeColumnPlugin($columnName, $table)
    {
        $type = isset(Model_Form::$typeMap[$table['columns'][$columnName]['scheme']['dataType']])
            ? Model_Form::$typeMap[$table['columns'][$columnName]['scheme']['dataType']]
            : 'text';

        return ['type' => $type, 'roles' => ['ROLE_ICE_GUEST', 'ROLE_ICE_USER']];
    }
}