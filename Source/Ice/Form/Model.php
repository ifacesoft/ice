<?php
/**
 * Ice form implementation model class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Form;

use Ice\Core\Form;
use Ice\Core\Logger;
use Ice\Core\Model as Core_Model;

/**
 * Class Model
 *
 * Binds forms and submit data for model objects
 *
 * @see Ice\Core\Form
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Form
 *
 * @version 0.1
 * @since 0.0
 */
class Model extends Form
{
    /**
     * @var Core_Model
     */
    private $_modelClass = null;

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
        'point' => Form::FIELD_GEO,
        'bigint' => Form::FIELD_NUMBER,
        'text' => Form::FIELD_TEXTAREA,
        'double' => Form::FIELD_TEXT,
        'longtext' => Form::FIELD_TEXT
    ];

    /**
     * Constructor for model forms
     *
     * @param Core_Model $modelClass
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.0
     */
    protected function __construct($modelClass)
    {
        parent::__construct($modelClass);

        $this->_modelClass = Model::getClass($modelClass);

        $validateScheme = $modelClass::getValidateScheme();

        foreach ($modelClass::getFormFieldTypes() as $fieldName => $fieldType) {
            $this->$fieldType(
                $fieldName,
                $modelClass::getFieldTitle($fieldName),
                $modelClass::getFieldPlaceholder($fieldName),
                $validateScheme[$fieldName]
            );
        }
    }

    /**
     * @return Core_Model
     */
    public function getModelClass()
    {
        return $this->_modelClass;
    }

//    /**
//     * Binds all model field values
//     *
//     * @param Core_Model $model
//     * @return $this
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
        $modelClass = $this->getModelClass();

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
        if ($error = $this->validate()) {
            Form::getLogger()->fatal($error, __FILE__, __LINE__);
        }

        /** @var Model $modelClass */
        $modelClass = $this->getKey();
        $modelClass::create($this->getValues())->insertOrUpdate();
    }
}