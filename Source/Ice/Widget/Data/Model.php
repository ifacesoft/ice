<?php

namespace Ice\Widget\Data;

use Ice\Core\Model as Core_Model;
use Ice\Core\Widget_Data;

class Model extends Table
{
    /**
     * @var Core_Model
     */
    private $_modelClass = null;

    /**
     * Return instance of table data
     *
     * @param  null $key
     * @param  null $ttl
     * @return Model
     */
    public static function getInstance($key = null, $ttl = null)
    {
        return parent::getInstance($key, $ttl);
    }

    protected static function create($key)
    {
        $data = parent::create($key);

        /**
         * @var Core_Model $modelClass
         */
        $modelClass = $data->getKey();

        foreach ($modelClass::getPlugin(Widget_Data::getClass()) as $fieldName => $columnType) {
            $data->$columnType(
                $fieldName,
                $modelClass::getFieldTitle($fieldName)
            );
        }

        return $data;
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
