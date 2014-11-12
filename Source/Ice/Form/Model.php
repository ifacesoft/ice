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
 * @version 0.0
 * @since 0.0
 */
class Model extends Form
{
    /**
     * Target model class
     *
     * @var Core_Model
     */
    private $_modelClass = null;

    /**
     * Constructor for model forms
     *
     * @param Core_Model $modelClass
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    protected function __construct($modelClass)
    {
        $this->_modelClass = $modelClass;

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
     * Binds all model field values
     *
     * @param Core_Model $model
     * @return $this
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function bindModel(Core_Model $model)
    {
        return $this->bind(array_merge($model->get(), $model->getPk()));
    }

    /**
     * Get tarret model class
     *
     * @return Core_Model
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getModelClass()
    {
        return $this->_modelClass;
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
        $modelClass = $this->getModelClass();
        $modelClass::create($this->getValues())->insertOrUpdate();
    }
}