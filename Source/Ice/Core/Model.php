<?php
/**
 * Ice core model abstract class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

use Ice\Core;
use Ice\Core\Model\Collection;
use Ice\Form\Model as Form_Model;
use Ice\Helper\Date;
use Ice\Helper\Json;
use Ice\Helper\Object;
use Ice\Helper\Spatial;

/**
 * Class Model
 *
 * Core module abstract class
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Core
 *
 * @version stable_0
 * @since stable_0
 */
abstract class Model
{
    use Core;

    /**
     * Primary key
     *
     * @var mixed
     */
    private $_pk = null;

    /**
     * Primary key name
     *
     * @var string
     */
    private $_pkName = null;

    /**
     *  Model fields
     *
     * @var array
     */
    private $_row = [];

    /**
     * Extended fields json
     *
     * @var array
     */
    private $_json = [];

    /**
     * Extended fields for geo data
     *
     * @var array
     */
    private $_geo = [];

    /**
     * Extended fields by primary key
     *
     * @var array
     */
    private $_fk = [];

    /**
     * Extended data of model
     *
     * @var array
     */
    private $_data = [];

    /**
     * Affected fields
     *
     * @var array
     */
    private $_affected = [];

    /**
     * Private constructor. Create model: Model::create()
     *
     * @param array $row
     * @param null $pk
     * @throws Exception
     */
    private function __construct(array $row, $pk = null)
    {
        $this->_pk = $pk;

        /** @var Model $modelClass */
        $modelClass = self::getClass();

        $columns = $modelClass::getScheme()->getColumnNames();
        $fields = $modelClass::getMapping();

        $this->_pkName = strtolower($modelClass::getClassName()) . '_pk';
        unset($fields[$this->_pkName]);

        if (!empty($row[$this->_pkName])) {
            if (!empty($this->_pk) && $row[$this->_pkName] != $this->_pk) {
                throw new Exception('Ambiguous pk: ' . var_export($row[$this->_pkName], true) . ' or ' . var_export($this->_pk, true));
            }

            $this->_pk = $row[$this->_pkName];
            unset($row[$this->_pkName]);
        }

        foreach ($fields as $fieldName => $columnName) {
            $this->_row[$fieldName] = null;

            if (array_key_exists($fieldName, $row)) {
                $this->set($fieldName, $row[$fieldName]);
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

            $default = $columns[$columnName]['default'];

            if ($default) {
                if ($default == 'CURRENT_TIMESTAMP') {
                    $default = Date::get();
                }

                $this->set($fieldName, $default, false);
            }
        }

        $this->_data = $row;
    }

    /**
     * Get class of model
     *
     * @param Model $modelClass
     *  class of short class (for example: Ice:User -> /Ice/Model/Ice/User)
     *
     * @return Model
     */
    public static function getClass($modelClass = null)
    {
        if (!$modelClass) {
            $modelClass = get_called_class();
        }

        /** @var Model $modelClass */
        $modelClass = Object::getClass(__CLASS__, $modelClass);

        if (in_array('Ice\Core\Model\Factory_Delegate', class_implements($modelClass))) {
            $modelClass = get_parent_class($modelClass);

            return $modelClass::getClass();
        }

        return $modelClass;
    }

    /**
     * Return scheme of table in data source: 'columnNames => (types, defaults, comments)')
     *
     * @return Model_Scheme
     */
    public static function getScheme()
    {
        return Model_Scheme::getInstance(self::getClass());
    }

    /**
     * Return model mapping
     *
     * @return array
     */
    public static function getMapping()
    {
        return self::getConfig()->gets('mapping');
    }

    /**
     * Return model validate scheme
     *
     * @return array
     */
    public static function getValidateScheme()
    {
        return self::getConfig()->gets(Validator::getClass());
    }

    /**
     * Return form field types
     *
     * @return array
     */
    public static function getFormFieldTypes()
    {
        return self::getConfig()->gets(Form::getClass());
    }

    /**
     * Method set value of model field
     *
     * example usage:
     * ```php
     *  $user->set('/name', 'Guest'); // set value 'Guest' for field 'user_name'
     *  $user->set(['user_name' => 'Name', '/surname' => 'Surname']); // sets array params
     *  $user->set('data', ['data1' => 'string1', 'data2' => 'string2']); // set value of field data__json
     * ```
     *
     * @param $fieldName
     * @param null $fieldValue
     * @param bool $isAffected Save for update
     * @return Model
     * @throws Exception
     */
    public function set($fieldName, $fieldValue = null, $isAffected = true)
    {
        if (is_array($fieldName)) {
            foreach ($fieldName as $key => $value) {
                $this->set($key, $value, $isAffected);
            }

            return $this;
        }

        if ($fieldName instanceof Model) {
            /** @var Model $fieldModelClass */
            $fieldModelClass = get_class($fieldName);
            return $this->set(strtolower($fieldModelClass::getClassName()) . '__fk', $fieldName->getPk(), $isAffected);
        }

        $fieldName = $this->getFieldName($fieldName);

        if ($this->getPkName() == $fieldName) {
            if ($isAffected && $this->_pk != $fieldValue) {
                $this->_affected[$fieldName] = $fieldValue;
            }

            $this->_pk = $fieldValue;

            return $this;
        }

        if (array_key_exists($fieldName, $this->_row)) {
            if ($isAffected && $this->_row[$fieldName] != $fieldValue) {
                $this->_affected[$fieldName] = $fieldValue;
            }

            $this->_row[$fieldName] = $fieldValue;

            return $this;
        }

        $jsonFieldName = $fieldName . '__json';
        if (array_key_exists($jsonFieldName, $this->_row)) {
            if ($fieldValue === null) {
                $this->_json[$fieldName] = [];
                return $this->set($jsonFieldName, Json::encode($this->_json[$fieldName]), $isAffected);
            }

            if (!is_array($fieldValue)) {
                $fieldValue = [$fieldValue];
            }

            $values = empty($this->_json[$fieldName]) ? [] : $this->_json[$fieldName];

            foreach ($fieldValue as $key => $value) {
                if ($value instanceof Model) {
                    $values[$value::getClass()] = $value->getPk();
                } else {
                    $values[$key] = $value;
                }
            }

            $this->_json[$fieldName] = $values;
            return $this->set(
                $jsonFieldName,
                Json::encode(array_merge($this->get($fieldName), $this->_json[$fieldName])),
                $isAffected
            );
        }

        $geoFieldName = $fieldName . '__geo';
        if (array_key_exists($geoFieldName, $this->_row)) {
            if ($fieldValue == null) {
                $this->_geo[$fieldName] = null;
                return $this->set($geoFieldName, null, $isAffected);
            }

            $this->_geo[$fieldName] = $fieldValue;
            $this->set(
                $geoFieldName,
                Spatial::encode($this->_geo[$fieldName]),
                $isAffected
            );
        }

        $fkFieldName = $fieldName . '__fk';
        if (array_key_exists($fkFieldName, $this->_row)) {
            if ($fieldValue == null) {
                $this->_fk[$fieldName] = null;
                return $this->set($fkFieldName, null, $isAffected);
            }

            $this->_fk[$fieldName] = $fieldValue;
            /** @var Model $fieldValue */
            return $this->set($fkFieldName, $fieldValue->getPk(), $isAffected);
        }

        /** @var Model $modelClass */
        $modelClass = get_class($this);
        throw new Exception('Could not set value:' . "\n" . print_r($fieldValue, true) .
            'Field "' . $fieldName . '" not found in Model "' . $modelClass::getClassName() . '"');
    }

    /**
     * Get primary key of model
     *
     * @return mixed
     */
    public function getPk()
    {
        return $this->_pk;
    }

    /**
     * Gets full model field name if send short name (for example: '/name' for model User -> user_name)
     *
     * @param $fieldName
     * @return string
     */
    public static function getFieldName($fieldName)
    {
        $fieldName = trim($fieldName);

        $isShort = strpos($fieldName, '/');

        if ($isShort === false) {
            return $fieldName;
        }

        $modelClass = self::getClass();

        $modelSchemeName = $isShort
            ? substr($fieldName, 0, $isShort)
            : $modelClass::getClassName();

        return strtolower($modelSchemeName) . '_' . substr($fieldName, $isShort + 1);
    }

    /**
     * Get field name of primary key
     *
     * @return string
     */
    public function getPkName()
    {
        return $this->_pkName;
    }

    /**
     * Get value of model field
     *
     * @param null $fieldName
     * @param bool $isNotNull
     * @throws Exception
     * @return mixed
     */
    public function get($fieldName = null, $isNotNull = true)
    {
        if ($fieldName === null) {
            return $this->_row;
        }

        $fieldName = $this->getFieldName($fieldName);

        if ($this->getPkName() == $fieldName) {
            return $this->getPk();
        }

        /** @var Model $modelClass */
        $modelClass = get_class($this);
        $modelName = $modelClass::getClassName();

        foreach (array($this->_row, $this->_json, $this->_fk) as $fields) {
            if (array_key_exists($fieldName, $fields)) {
                if ($isNotNull && $fields[$fieldName] === null) {
                    throw new Exception('field "' . $fieldName . '" of model "' . $modelName . '" is null');
                }
                return $fields[$fieldName];
            }
        }

        $jsonFieldName = $fieldName . '__json';
        if (array_key_exists($jsonFieldName, $this->_row)) {
            $json = Json::decode($this->_row[$jsonFieldName]);

            if (empty($json)) {
                return [];
            }

            $this->_json[$fieldName] = $json;
            return $this->_json[$fieldName];
        }

        $geoFieldName = $fieldName . '__geo';
        if (array_key_exists($geoFieldName, $this->_row)) {
            $geo = Spatial::decode($this->_row[$geoFieldName]);

            if (empty($geo)) {
                return [];
            }

            $this->_geo[$fieldName] = $geo;
            return $this->_geo[$fieldName];
        }

        /** @var Model $fieldName */
        $fieldName = Model::getClass($fieldName);

        // one-to-many
        $foreignKeyName = strtolower(Object::getName($fieldName)) . '__fk';
        if (array_key_exists($foreignKeyName, $this->_row)) {
            $key = $this->_row[$foreignKeyName];

            if (!$key) {
                throw new Exception('Model::__get: Не определен внешний ключ ' . $foreignKeyName . ' в модели ' . $modelName);
            }

            $row = array_merge($this->_data, [strtolower(Object::getName($fieldName)) . '_pk' => $key]);
            $joinModel = $fieldName::create($row);

            if (!$joinModel) {
                throw new Exception('Model::__get: Не удалось получить модель по внешнему ключу ' .
                    $foreignKeyName . '="' . $key . '" в модели ' . $modelName);
            }

            $this->_fk[$fieldName] = $joinModel;
            return $this->_fk[$fieldName];
        }

        // TODO: Пока лениво подгружаем
        // many-to-one
        $foreignKeyName = strtolower($modelName) . '__fk';
        if (array_key_exists($foreignKeyName, $fieldName::getMapping())) {
            $this->_fk[$fieldName] = $fieldName::getQueryBuilder()
                ->select('*')
                ->eq($foreignKeyName, $this->getPk())
                ->getQuery()
                ->getData()
                ->getCollection();

            return $this->_fk[$fieldName];
        }

        throw new Exception('Field "' . $fieldName . '" not found in Model "' . $modelName . '"');
    }

    /**
     * Create model instance
     *
     * @param array $row
     * @param null $pk
     * @return Model
     */
    public static function create(array $row = [], $pk = null)
    {
        /** @var Model $modelClass */
        $modelClass = get_called_class();

        if (isset(class_parents($modelClass)[Model_Factory::getClass()])) {
            $modelClass = $modelClass . '_' . $row[$modelClass::getFieldName('/delegate_name')];
        }

        return new $modelClass($row, $pk);
    }

    /**
     * Return queryBuilder
     *
     * @param null $tableAlias
     * @return Query_Builder
     */
    public static function  getQueryBuilder($tableAlias = null)
    {
        return Query_Builder::getInstance(self::getClass(), $tableAlias);
    }

    /**
     * Get dataSource for current model class
     *
     * @return Data_Source
     */
    public static function getDataSource()
    {
        $modelName = self::getClass();
        $parentModelName = get_parent_class($modelName);

        if ($parentModelName == Model_Defined::getClass() || $parentModelName == Model_Factory::getClass()) {
            return Data_Source::getInstance(Object::getName($parentModelName) . ':model/' . $modelName);
        }

        return Data_Source::getInstance();
    }

    /**
     * Return full field names
     *
     * @param array $fields
     * @throws Exception
     * @return array
     */
    public static function getFieldNames($fields = [])
    {
        $modelClass = self::getClass();

        $fieldNames = array_keys($modelClass::getMapping());

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

    /**
     * Return collection of current model class name
     *
     * @return Collection
     */
    public static function getCollection()
    {
        return Collection::create(self::getClass());
    }

    /**
     * Return all rows for self model class
     *
     * @param $fieldNames
     * @return array
     */
    public static function getRows($fieldNames)
    {
        return self::getQueryBuilder()
            ->select($fieldNames)
            ->getQuery()
            ->getData()
            ->getRows();
    }

    /**
     * Return localized title of table
     *
     * @param null $tableName
     * @return string
     */
    public static function getTitle($tableName = null)
    {
        if (empty($tableName)) {
            $tableName = self::getTableName();
        }

        return self::getResource()->get($tableName);
    }

    /**
     * Return localized title of field
     *
     * @param $fieldName
     * @return string
     */
    public static function getFieldTitle($fieldName)
    {
        return self::getResource()->get($fieldName);
    }

    /**
     * Return localized title of placeholder field
     *
     * @param $fieldName
     * @return string
     */
    public static function getFieldPlaceholder($fieldName)
    {
        return self::getResource()->get($fieldName . '_placeholder');
    }

    /**
     * Return table name of self model class
     *
     * @return string
     */
    public static function getTableName()
    {
        $modelClass = self::getClass();
        return Data_Scheme::getInstance($modelClass::getScheme()->getScheme())->getModelClasses()[$modelClass];
    }

    /**
     * Return form of self model class
     *
     * @param array $filterFields
     * @return Form_Model
     */
    public static function getForm(array $filterFields = [])
    {
        return Form_Model::getInstance(self::getClass())->addFilterFields($filterFields);
    }

    /**
     * Magic get
     *
     * @deprecated use ->get($fieldName)
     *
     * @see Model::get()
     *
     * @param $fieldName
     * @return mixed
     * @throws Exception
     */
    public function __get($fieldName)
    {
        return $this->get($fieldName);
    }

    /**
     * Magic set
     *
     * @deprecated use ->set($fieldName, $value)
     *
     * @see Model::set()
     *
     * @param $fieldName
     * @param $value
     * @return Model
     * @throws Exception
     */
    public function __set($fieldName, $value)
    {
        return $this->set($fieldName, $value);
    }

    /**
     * Get value from data of model
     *
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
     * Set data in model data
     *
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

    /**
     * Execute insert or update model data
     *
     * @param null $sourceName
     * @return Model|null
     */
    public function insertOrUpdate($sourceName = null)
    {
        $pk = $this->getPk(); // todo: may be (bool) $this->getPk()

        if (empty($pk)) {
            return $this->insert($sourceName);
        }

        /** @var Model $class */
        $class = get_class($this);

        return $class::getModel($this->getPk())
            ? $this->update($sourceName)
            : $this->insert($sourceName);
    }

    /**
     * Return model by primary key
     *
     * @param $pk
     * @param array|string $fieldNames
     * @param string $sourceName
     * @throws Exception
     * @return Model|null
     */
    public static function getModel($pk, $fieldNames = '/pk', $sourceName = null)
    {
        return self::getBy('pk', $pk, $fieldNames, $sourceName);
    }

    /**
     * Return by custom field
     *
     * @param $shortFieldName
     * @param $fieldValue
     * @param $fieldNames
     * @param null $sourceName
     * @return Model|null
     */
    public static function getBy($shortFieldName, $fieldValue, $fieldNames, $sourceName = null)
    {
        return self::getQueryBuilder()
            ->select($fieldNames)
            ->eq('/' . $shortFieldName, $fieldValue)
            ->limit(1)
            ->getQuery($sourceName)
            ->getData()
            ->getModel();
    }

    /**
     * Execute update for model
     *
     * @param $fieldName
     * @param null $value
     * @param null $sourceName
     * @throws Exception
     * @return Model|null
     */
    public function update($fieldName = null, $value = null, $sourceName = null)
    {
        if ($fieldName) {
            $this->set($fieldName, $value);
        }

        /** @var Model $modelClass */
        $modelClass = get_class($this);

        $modelClass::getQueryBuilder()
            ->update($this->_affected)
            ->pk($this->getPk())
            ->getQuery($sourceName)
            ->getData();

        $this->_affected = [];

        return $this;
    }

    /**
     * Execute insert into data source
     *
     * @param string $sourceName
     * @throws Exception
     * @return Model|null
     */
    public function insert($sourceName = null)
    {
        $values = $this->get();

        if ($this->_pk) {
            $values[$this->getPkName()] = $this->_pk;
        }

        /** @var Model $modelClass */
        $modelClass = get_class($this);

        $this->_pk = $modelClass::getQueryBuilder()
            ->insert($values)
            ->getQuery($sourceName)
            ->getData()
            ->getInsertId();

        $this->_affected = [];

        return $this;
    }

    /**
     * Execute delete for model
     *
     * @param null $sourceName
     * @return Model|null
     */
    public function delete($sourceName = null)
    {
        /** @var Model $modelClass */
        $modelClass = get_class($this);

        $modelClass::getQueryBuilder()
            ->delete($this->getPk())
            ->getQuery($sourceName)
            ->getData();

        return $this;
    }

    /**
     * Return array of extended fields by foreign keys
     *
     * @return array
     */
    public function getFk()
    {
        return $this->_fk;
    }

    /**
     * Return array of extended fields by json fields
     *
     * @return array
     */
    public function getJson()
    {
        return $this->_json;
    }

    /**
     * Get array of fields names end their values
     *
     * @return array
     */
    public function getRow()
    {
        return $this->_row;
    }

    /**
     * Casts model to string
     *
     * @return string
     */
    public function __toString()
    {
        return (string)self::getClass();
    }

    /**
     * Return link of model
     *
     * @param Model $modelClass
     * @param mixed $modelPk
     * @param Model|null $linkModelClass
     * @return Model|null
     */
    public function getLink($modelClass, $modelPk, $linkModelClass = null)
    {
        /** @var Model $selfClass */
        $selfClass = get_class($this);

        $selfClassName = $selfClass::getClassName();
        $className = $modelClass::getClassName();

        if (!$linkModelClass) {
            $namespace = $selfClass::getNamespace();

            $modelClasses = Data_Scheme::getInstance()->getModelClasses();

            /** @var Model $linkModelClass */
            $linkModelClass = isset($modelClasses[$namespace . $selfClassName . '_' . $className . '_Link'])
                ? $namespace . $selfClassName . '_' . $className . '_Link'
                : $namespace . $className . '_' . $selfClassName . '_Link';

        }

        return $linkModelClass::getQueryBuilder()
            ->select('*')
            ->eq(strtolower($selfClassName) . '__fk', $this->getPk())
            ->eq(strtolower($className) . '__fk', $modelPk)
            ->getQuery()
            ->getData()
            ->getModel();

    }
}