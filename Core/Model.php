<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 28.12.13
 * Time: 23:41
 */

namespace ice\core;

use ice\core\helper\Date;
use ice\core\helper\Json;
use ice\core\helper\Object;
use ice\core\model\Collection;
use ice\core\model\Factory;
use ice\Exception;

class Model
{
    private $_row = array();
    private $_json = array();
    private $_fk = array();
    private $_data = array();
    private $_updates = array();

    private function __construct(array $row)
    {
        /** @var Model $modelClass */
        $modelClass = self::getClass();

        $modelSchemeColumns = $modelClass::getScheme()->getColumns();
        $modelMappingFields = $modelClass::getMapping()->getFieldNames();

        foreach ($modelMappingFields as $fieldName => $columnName) {

            $this->_row[$fieldName] = null;

            if (array_key_exists($fieldName, $row)) {
                $this->set($fieldName, $row[$fieldName], false);
                unset($row[$fieldName]);
                continue;
            }

            foreach (array('__json', '__fk', '_geo') as $ext) {
                $field = strstr($fieldName, $ext, true);
                if ($field !== false && array_key_exists($field, $row)) {
                    $this->set($field, $row[$field], false);
                    unset($row[$field]);
                    continue 2;
                }
            }

            $default = $modelSchemeColumns[$columnName]['default'];

            if ($default) {
                if ($default == 'CURRENT_TIMESTAMP') {
                    $default = Date::getCurrent();
                }

                $this->set($columnName, $default);
            }
        }

        $this->_data = $row;
    }

    /**
     * @param Model $modelClass
     * @return Model
     */
    public static function getClass($modelClass = null)
    {
        if (!$modelClass) {
            $modelClass = get_called_class();
        }

        $parts = explode(':', $modelClass);

        if (count($parts) == 2) {
            list($prefix, $modelName) = $parts;
            $modelClass = Data_Mapping::get()->getPrefixes()[$prefix] . $modelName;
        }

        if (in_array('ice\core\model\Factory_Delegate', class_implements($modelClass))) {
            $modelClass = get_parent_class($modelClass);

            return $modelClass::getClass();
        }

        return $modelClass;
    }

    public static function getScheme()
    {
        return Model_Scheme::get(self::getClass());
    }

    public static function getMapping()
    {
        return Model_Mapping::get(self::getClass());
    }

    public function set($fieldName, $fieldValue = null, $isUpdate = true)
    {
        if (is_array($fieldName)) {
            $set = array();

            foreach ($fieldName as $key => $value) {
                array_merge($set, $this->set($key, $value, $isUpdate));
            }

            return $set;
        }

        $fieldName = $this->getFieldName($fieldName);

        if (array_key_exists($fieldName, $this->_row)) {
//            var_dump($fieldName, $isUpdate, $this->_row[$fieldName] != $fieldValue, $this->_row[$fieldName], $fieldValue, "\n\n");
            if ($isUpdate && $this->_row[$fieldName] != $fieldValue) {
                $this->_updates[$fieldName] = $fieldValue;
            }

            $this->_row[$fieldName] = $fieldValue;

            return array(
                array(
                    $fieldName => $this->_row[$fieldName]
                )
            );
        }

        $jsonFieldName = $fieldName . '__json';
        if (array_key_exists($jsonFieldName, $this->_row)) {
            if ($fieldValue == null) {
                $this->_json[$fieldName] = array();
                return $this->set($jsonFieldName, Json::encode($this->_json[$fieldName]));
            }

            $this->_json[$fieldName] = $fieldValue;

            return $this->set(
                $jsonFieldName,
                Json::encode(array_merge($this->get($fieldName), $this->_json[$fieldName])),
                $isUpdate
            );
        }

        $fkFieldName = $fieldName . '__fk';
        if (array_key_exists($fkFieldName, $this->_row)) {
            if ($fieldValue == null) {
                $this->_fk[$fieldName] = null;
                return $this->set($fkFieldName, null);
            }

            $this->_fk[$fieldName] = $fieldValue;
            return $this->set($fkFieldName, $fieldValue->getPk());
        }

        throw new Exception('Could not set value:' . "\n" .
            print_r($fieldValue, true) .
            'Field "' . $fieldName . '" not found in Model "' . $this->getModelName() . '"');
    }

    public static function getFieldName($fieldName)
    {
        $isShort = strpos($fieldName, '/');

        if ($isShort === false) {
            return $fieldName;
        }

        $modelClass = self::getClass();

        $modelSchemeName = $isShort
            ? substr($fieldName, 0, $isShort)
            : $modelClass::getModelName();

        return strtolower($modelSchemeName) . '_' . substr($fieldName, $isShort + 1);
    }

    public static function getModelName()
    {
        return Object::getName(self::getClass());
    }

    public function get($fieldName = null)
    {
        if ($fieldName === null) {
            return $this->_row;
        }

        $fieldName = $this->getFieldName($fieldName);

        foreach (array($this->_row, $this->_json, $this->_fk) as $fields) {
            if (array_key_exists($fieldName, $fields)) {
                return $fields[$fieldName];
            }
        }

        $jsonFieldName = $fieldName . '__json';
        if (array_key_exists($jsonFieldName, $this->_row)) {
            $json = Json::decode($this->_row[$jsonFieldName]);

            if (empty($json)) {
                return array();
            }

            $this->_json[$fieldName] = $json;
            return $this->_json[$fieldName];
        }

        $foreignKeyName = strtolower(Object::getName($this->getClass($fieldName))) . '__fk';
        if (array_key_exists($foreignKeyName, $this->_row)) {
            $fieldName = $this->getClass($fieldName);
            $key = $this->_row[$foreignKeyName];

            if (!$key) {
                throw new Exception('Model::__get: Не определен внешний ключ ' . $foreignKeyName . ' в модели ' . $this->getModelName());
            }

            $row = array_merge($this->_data, array(strtolower(Object::getName($fieldName)) . '_pk' => $key));
            $joinModel = $fieldName::create($row);

            if (!$joinModel) {
                throw new Exception('Model::__get: Не удалось получить модель по внешнему ключу ' .
                    $foreignKeyName . '="' . $key . '" в модели ' . $this->getModelName());
            }

            $this->_fk[$fieldName] = $joinModel;
            return $this->_fk[$fieldName];
        }

        throw new Exception('Field "' . $fieldName . '" not found in Model "' . $this->getModelName() . '"');
    }

    /**
     * @param array $row
     * @return Model
     */
    public static function create(array $row)
    {
        /** @var Model $modelClass */
        $modelClass = get_called_class();
        if (isset(class_parents($modelClass)[Factory::getClass()])) {
            $modelClass = $modelClass . '_' . $row[$modelClass::getFieldName('/delegate_name')];

            return new $modelClass($row);
        }

        return new $modelClass($row);
    }

    public static function getDataSource()
    {
        $modelName = self::getClass();
        $parentModelName = get_parent_class($modelName);

        if ($parentModelName == __CLASS__) {
            return Data_Source::getDefault();
        }

        return Data_Source::get(substr($parentModelName, strlen('Ice\core\model\\')) . ':model/' . $modelName);
    }

    /**
     * Получение модели по первичному ключу
     *
     * @param $pk
     * @param array $fieldNames
     * @param Data_Source $dataSource
     * @return Model|null
     */
    public static function getModel($pk, $fieldNames = array(), Data_Source $dataSource = null)
    {
        $modelClass = self::getClass();

        $model = Model_Repository::get($modelClass, $pk);

        if ($model) {
            return $model;
        }

        $model = Model::byQuery($modelClass::getQueryBuilder()->select($fieldNames)->pk($pk), $dataSource);

        if ($model) {
            Model_Repository::set($modelClass, $pk, $model);
        }

        return $model;
    }

    /**
     * Получение имен полей модели
     *
     * @param array $fields
     * @throws Exception
     * @return array
     */
    public static function getFieldNames($fields = array())
    {
        $modelClass = self::getClass();

        $fieldNames = array_keys($modelClass::getMapping()->getFieldNames());

        if (empty($fields)) {
            return $fieldNames;
        }

        foreach ((array)$fields as &$fieldName) {
            $fieldName = $modelClass::getFieldName($fieldName);

            if (in_array($fieldName, $fieldNames)) {
                continue;
            }

            if (in_array($fieldName . '__json', $fieldNames)) {
                $fieldName = $fieldName . '__json';
                continue;
            }

            throw new Exception('Поле "' . $fieldName . '" не найдено в модели "' . self::getClass() . '"');
        }

        return $fieldNames;
    }

    public static function byQuery(Query $query, $fieldNames = array(), Data_Source $dataSource = null)
    {
        return Collection::byQuery($query->limit(1), $fieldNames, $dataSource)->first();

    }

    /**
     * @param string $statementType
     * @param null $tableAlias
     * @return Query
     */
    public static function getQueryBuilder($statementType = 'select', $tableAlias = null)
    {
        return Query::getInstance($statementType, self::getClass(), $tableAlias);
    }

    /**
     * @param null $key
     * @return array
     */
    public function getData($key = null)
    {
        if ($key === null) {
            return $this->_data;
        }

        return isset($this->_data[$key]) ? $this->_data[$key] : null;
    }

    /**
     * @param $key
     * @param null $value
     */
    public function setData($key, $value = null)
    {
        if (is_array($key) && !$value) {
            $this->_data = array_merge($this->_data, $key);
            return;
        }

        $this->_data[$key] = $value;
    }

    public function getPk()
    {
        /** @var Model $modelClass */
        $modelClass = $this->getClass();
        return $this->_row[$modelClass::getPkName()];
    }

    public static function getPkName()
    {
        /** @var Model $modelName */
        $modelName = self::getClass();
        return strtolower($modelName::getModelName()) . '_pk';
    }

    public function insert(Data_Source $dataSource = null)
    {
        /** @var Model $modelClass */
        $modelClass = $this->getClass();
        return $modelClass::getCollection()->add($this)->insert($dataSource)->first();
    }

    /**
     * @param array $fieldNames
     * @return Collection
     */
    public static function getCollection(array $fieldNames = array())
    {
        return Collection::create(self::getClass(), $fieldNames);
    }

    public function update($key, $value = null, Data_Source $dataSource = null)
    {
        $this->set($key, $value);

        /** @var Model $modelClass */
        $modelClass = $this->getClass();
        return $modelClass::getCollection()->add($this)->update($this->getUpdates(), $dataSource)->first();
    }

    /**
     * @return array
     */
    public function getUpdates()
    {
        return $this->_updates;
    }

    /**
     * @return array
     */
    public function getFk()
    {
        return $this->_fk;
    }

    /**
     * @return array
     */
    public function getJson()
    {
        return $this->_json;
    }

    /**
     * @return array
     */
    public function getRow()
    {
        return $this->_row;
    }

    public function __toString()
    {
        return (string)$this->getClass();
    }
}