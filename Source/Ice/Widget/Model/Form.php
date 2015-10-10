<?php
/**
 * Ice form implementation model class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Widget;

use Ice\Core\Model as Core_Model;
use Ice\Core\Validator;

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
class Model_Form extends Form
{
    /**
     * Field type map
     *
     * @var array
     */
    public static $typeMap = [
        'int' => Form::FIELD_NUMBER,
        'varchar' => Form::FIELD_TEXT,
        'datetime' => Form::FIELD_DATE,
        'timestamp' => Form::FIELD_DATE,
        'tinyint' => Form::FIELD_CHECKBOX,
        'point' => Form::FIELD_MAP,
        'bigint' => Form::FIELD_NUMBER,
        'text' => Form::FIELD_TEXTAREA,
        'double' => Form::FIELD_TEXT,
        'longtext' => Form::FIELD_TEXT
    ];

    /**
     * Constructor for model forms
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since   0.0
     */
    protected function __construct()
    {
        /**
         * @var Core_Model $modelClass
         */
        $modelClass = $this->getValues();

        $validateScheme = $modelClass::getPlugin(Validator::getClass());

        $pkFieldNames = $modelClass::getScheme()->getPkFieldNames();

        foreach ($modelClass::getPlugin(__CLASS__) as $fieldName => $fieldType) {
            $this->$fieldType(
                $fieldName,
                $modelClass::getFieldTitle($fieldName),
                [
                    'placeholder' => $modelClass::getFieldPlaceholder($fieldName),
                    'readonly' => in_array($fieldName, $pkFieldNames)
                ],
                $validateScheme[$fieldName]
            );
        }
    }

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

    /**
     * Widget config
     *
     * @return array
     *
     *  protected static function config()
     *  {
     *      return [
     *          'render' => ['template' => null, 'class' => 'Ice:Php', 'layout' => null, 'resource' => null],
     *          'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => 'Widget: Access denied!'],
     *          'resource' => ['js' => null, 'css' => null, 'less' => null, 'img' => null],
     *          'cache' => ['ttl' => -1, 'count' => 1000],
     *          'input' => [],
     *          'output' => [],
     *          'action' => [
     *          //  'class' => 'Ice:Render',
     *          //  'params' => [
     *          //      'widgets' => [
     *          ////        'Widget_id' => Widget::class
     *          //      ]
     *          //  ],
     *          //  'url' => true,
     *          //  'method' => 'POST',
     *          //  'callback' => null
     *          ]
     *      ];
     *  }
     *
     * /** Build widget
     *
     * @param array $input
     * @return array
     */
    protected function build(array $input)
    {
        // TODO: Implement build() method.
    }

    public static function schemeColumnPlugin($columnName, $table)
    {
        return isset(Model_Form::$typeMap[$table['columns'][$columnName]['scheme']['dataType']])
            ? Model_Form::$typeMap[$table['columns'][$columnName]['scheme']['dataType']]
            : 'text';
    }
}