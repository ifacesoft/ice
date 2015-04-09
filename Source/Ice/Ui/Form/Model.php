<?php
/**
 * Ice form implementation model class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Ui\Form;

use Ice\Core\Model as Core_Model;
use Ice\Core\Ui_Form;
use Ice\Core\Validator;

/**
 * Class Model
 *
 * Binds forms and submit data for model objects
 *
 * @see Ice\Core\Ui_Form
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Ui_Form
 */
class Model extends Ui_Form
{
    /**
     * Field type map
     *
     * @var array
     */
    public static $typeMap = [
        'int' => Ui_Form::FIELD_NUMBER,
        'varchar' => Ui_Form::FIELD_TEXT,
        'datetime' => Ui_Form::FIELD_DATE,
        'timestamp' => Ui_Form::FIELD_DATE,
        'tinyint' => Ui_Form::FIELD_CHECKBOX,
        'point' => Ui_Form::FIELD_GEO,
        'bigint' => Ui_Form::FIELD_NUMBER,
        'text' => Ui_Form::FIELD_TEXTAREA,
        'double' => Ui_Form::FIELD_TEXT,
        'longtext' => Ui_Form::FIELD_TEXT
    ];

    /**
     * Constructor for model forms
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.0
     */
    protected function __construct()
    {
        /** @var Core_Model $modelClass */
        $modelClass = $this->getValues();

        $validateScheme = $modelClass::getPlugin(Validator::getClass());

        $pkFieldNames = $modelClass::getScheme()->getPkFieldNames();

        foreach ($modelClass::getPlugin(Ui_Form::getClass()) as $fieldName => $fieldType) {
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
//     * @return Ui_Form_Model
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
        /** @var Core_Model $modelClass */
        $modelClass = $this->getValues();

        foreach ($filterFields as &$filterField) {
            $filterField = $modelClass::getFieldName($filterField);
        }

        return parent::addFilterFields($filterFields);
    }

    /**
     * Submit form
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function submit()
    {
        /** @var Core_Model $modelClass */
        $modelClass = $this->getValues();
        $modelClass::create($this->validate())->save(true);
    }

    public function render()
    {
        // TODO: Implement render() method.
    }
}