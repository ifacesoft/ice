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
 * @version stable_0
 * @since stable_0
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
     * @throws \Ice\Core\Exception
     */
    public function bindModel(Core_Model $model)
    {
        return $this->bind(array_merge($model->get(), [$model->getPkName() => $model->getPk()]));
    }

    /**
     * Get tarret model class
     *
     * @return Core_Model
     */
    public function getModelClass()
    {
        return $this->_modelClass;
    }

    /**
     * Submit form
     *
     * @throws \Ice\Core\Exception
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