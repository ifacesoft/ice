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
use Ice\Core\Widget_Form;

/**
 * Class Model
 *
 * Binds forms and submit data for model objects
 *
 * @see Ice\Core\Widget_Form
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Widget_Form
 */
class Form_Model extends Form
{
    protected static function config()
    {
        return [
            'render' => ['template' => true, 'class' => 'Ice:Php', 'layout' => null, 'resource' => null],
            'input' => [],
            'access' => ['roles' => [], 'request' => null, 'env' => null]
        ];
    }

    /**
     * Field type map
     *
     * @var array
     */
    public static $typeMap = [
        'int' => Widget_Form::FIELD_NUMBER,
        'varchar' => Widget_Form::FIELD_TEXT,
        'datetime' => Widget_Form::FIELD_DATE,
        'timestamp' => Widget_Form::FIELD_DATE,
        'tinyint' => Widget_Form::FIELD_CHECKBOX,
        'point' => Widget_Form::FIELD_MAP,
        'bigint' => Widget_Form::FIELD_NUMBER,
        'text' => Widget_Form::FIELD_TEXTAREA,
        'double' => Widget_Form::FIELD_TEXT,
        'longtext' => Widget_Form::FIELD_TEXT
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

        foreach ($modelClass::getPlugin(Widget_Form::getClass()) as $fieldName => $fieldType) {
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
    //     * @return Widget_Form_Model
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
     * Submit form
     *
     * @param $token
     * @return array|void
     */
    protected function action($token)
    {
        /** @var Core_Model $modelClass */
        $modelClass = $this->getValues();
        $modelClass::create($this->validate())->save(true);
    }
}