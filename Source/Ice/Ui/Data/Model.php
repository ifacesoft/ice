<?php

namespace Ice\Ui\Data;

use Ice\Core\Model as Core_Model;
use Ice\Core\Ui_Data;

class Model extends Ui_Data
{
    /**
     * @var Core_Model
     */
    private $_modelClass = null;

    protected static function create($key)
    {
        $data = parent::create($key);

        /** @var Core_Model $modelClass */
        $modelClass = $data->getKey();

        foreach ($modelClass::getPlugin(Ui_Data::getClass()) as $fieldName => $columnType) {
            $data->$columnType(
                $fieldName,
                $modelClass::getFieldTitle($fieldName)
            );
        }

        return $data;
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

    public function render()
    {
        // TODO: Implement render() method.
    }
}