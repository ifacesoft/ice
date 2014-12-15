<?php

namespace Ice\Data;

use Ice\Core\Data;
use Ice\Core\Model as Core_Model;

class Model extends Data
{

    /**
     * @var Core_Model
     */
    private $_modelClass = null;

    /**
     * Constructor for model data
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

        foreach ($modelClass::getDataFieldTypes() as $fieldName => $columnType) {
            $this->$columnType(
                $fieldName,
                $modelClass::getFieldTitle($fieldName)
            );
        }
    }

    /**
     * Return instance of table data
     *
     * @param null $key
     * @param null $ttl
     * @return Model
     */
    public static function getInstance($key = null, $ttl = null)
    {
        return parent::getInstance($key, $ttl);
    }

    public function addFilterFields(array $filterFields)
    {
        $modelClass = $this->getModelClass();

        foreach ($filterFields as &$filterField) {
            $filterField = $modelClass::getFieldName($filterField);
        }

        return parent::addFilterFields($filterFields);
    }

    /**
     * @return Core_Model
     */
    public function getModelClass()
    {
        return $this->_modelClass;
    }
}