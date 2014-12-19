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
use Ice\Data\Model as Data_Model;
use Ice\Form\Model as Form_Model;
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
 * @version 0.0
 * @since 0.0
 */
abstract class Model
{
    use Core;

    /**
     * Primary key
     *
     * ```php
     *  $_pk = [
     *      PK_NAME => PK_VALUE // or null
     *      // ...
     *      // PK_NAME_2 => PK_VALUE_2 // Primary key with two columns
     * ];
     * ```
     *
     * @var array
     */
    private $_pk = null;

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
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    private function __construct(array $row, $pk = null)
    {
        $this->_pk = $pk;

        /** @var Model $modelClass */
        $modelClass = self::getClass();
        $lowercaseModelName = strtolower(self::getClassName());

        $modelScheme = $modelClass::getScheme();
        $fields = $modelClass::getMapping();
        $flippedFields = array_flip($fields);

        $pkColumnNames = $modelScheme->getIndexes()['PRIMARY KEY']['PRIMARY'];

        if ($pk !== null) {
            $this->_pk = is_array($pk) ? $pk : [$flippedFields[reset($pkColumnNames)] => $pk];
        }

        $detectedPk = [];
        foreach ($pkColumnNames as $pkColumnName) {
            $pkFieldName = $flippedFields[$pkColumnName];

            if (!empty($row[$pkFieldName])) {
                $detectedPk[$pkFieldName] = is_array($row[$pkFieldName]) ? reset($row[$pkFieldName]) : $row[$pkFieldName];
                unset($row[$pkFieldName]);
            }

            unset($fields[$pkFieldName]);
        }

        if (!empty($detectedPk)) {
            if (!empty($this->_pk) && $this->_pk != $detectedPk) {
                Model::getLogger()->fatal(['Ambiguous pk: {$0} or {$1}', [var_export($this->_pk, true), var_export($detectedPk, true)]], __FILE__, __LINE__);
            }

            $this->set($detectedPk);
            unset($detectedPk);
        }

//        $columns = $modelScheme->getColumnNames();

        foreach ($fields as $fieldName => $columnName) {
            $this->_row[$fieldName] = null;

            if (array_key_exists($fieldName, $row)) {
                $this->set($fieldName, $row[$fieldName]);
                unset($row[$fieldName]);
                continue;
            }

            $length = strlen($lowercaseModelName);
            if (strpos($fieldName, $lowercaseModelName) === 0) {
                $field = '/' . substr($fieldName, $length + 1);
                if (array_key_exists($field, $row)) {
                    $this->set($fieldName, $row[$field]);
                    unset($row[$field]);
                    continue;
                }
            }

            foreach (['__json', '__fk', '_geo'] as $ext) {
                $field = strstr($fieldName, $ext, true);
                if ($field !== false && array_key_exists($field, $row)) {
                    $this->set($field, $row[$field]);
                    unset($row[$field]);
                    continue 2;
                }
            }

//            $default = $columns[$columnName]['default'];
//
//            if ($default) {
//                if ($default == 'CURRENT_TIMESTAMP') {
//                    $default = Date::get();
//                }
//
//                $this->set($fieldName, $default, false);
//            }
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
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.1
     * @since 0.0
     */
    public static function getClass($modelClass = null)
    {
        if (!$modelClass) {
            /** @var Model $modelClass */
            $modelClass = get_called_class();
        }

        $modelClass = Object::getClass(__CLASS__, $modelClass);

//        if (Object::isShortName($modelClass)) {
//            list($moduleAlias, $objectName) = explode(':', $modelClass);
//
//            $modelClass = $moduleAlias . '\\' . str_replace('_', '\\', Object::getName(__CLASS__)) . '\\' . $moduleAlias . '\\' . $objectName;
//        }

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
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public static function getScheme()
    {
        return Model_Scheme::getInstance(self::getClass());
    }

    /**
     * Return model mapping
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public static function getMapping()
    {
        return self::getConfig()->gets('mapping');
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
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
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

        if ($this->isPkName($fieldName)) {
            if ($isAffected) {
                $this->_affected[$fieldName] = $fieldValue;
            }

            $this->_pk[$fieldName] = $fieldValue;

            return $this;
        }
        if (array_key_exists($fieldName, $this->_row)) {
            if ($isAffected) {
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
            $this->set($geoFieldName, Spatial::encode($this->_geo[$fieldName]), $isAffected);
        }

        $fkFieldName = $fieldName . '__fk';
        if (array_key_exists($fkFieldName, $this->_row)) {
            if ($fieldValue == null) {
                $this->_fk[$fieldName] = null;
                return $this->set($fkFieldName, null, $isAffected);
            }

            $this->_fk[$fieldName] = $fieldValue;
            /** @var Model $fieldValue */
            $pk = $fieldValue->getPk();
            return $this->set($fkFieldName, reset($pk), $isAffected);
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
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
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
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
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
     * Check is primary key value
     *
     * @param $fieldName
     * @return bool
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    private function isPkName($fieldName)
    {
        /** @var Model $modelClass */
        $modelClass = get_class($this);

        $modelMapping = $modelClass::getMapping();

        if (!isset($modelMapping[$fieldName])) {
            return false;
        }

        $columnName = $modelMapping[$fieldName];

        $primaryKeys = $modelClass::getScheme()->getIndexes()['PRIMARY KEY']['PRIMARY'];

        return in_array($columnName, $primaryKeys);
    }

    /**
     * Get value of model field
     *
     * @param null $fieldName
     * @param bool $isNotNull
     * @throws Exception
     * @return mixed
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function get($fieldName = null, $isNotNull = true)
    {
        if ($fieldName === null) {
            return $this->_row;
        }

        $fieldName = $this->getFieldName($fieldName);

        if ($this->isPkName($fieldName)) {
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
            $this->_fk[$fieldName] = $fieldName::query()
                ->eq($foreignKeyName, $this->getPk())
                ->select('*')
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
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
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
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public static function  query($tableAlias = null)
    {
        return Query_Builder::getInstance(self::getClass(), $tableAlias);
    }

    /**
     * Return model validate scheme
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public static function getValidateScheme()
    {
        return self::getConfig()->gets(Validator::getClass());
    }

    /**
     * Return form field types
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public static function getFormFieldTypes()
    {
        return self::getConfig()->gets(Form::getClass());
    }

    /**
     * Return data field types
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.2
     */
    public static function getDataFieldTypes()
    {
        return self::getConfig()->gets(Data::getClass());
    }

    /**
     * Get dataSource for current model class
     *
     * @return Data_Source
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
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
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
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
     * Return all rows for self model class
     *
     * @param array $paginator
     * @param string $fieldNames
     * @param null $sourceName
     * @param int $ttl
     * @return array
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.0
     */
    public static function getRows(array $paginator, $fieldNames = '*', $sourceName = null, $ttl = 3600)
    {
        return self::query()
            ->setPaginator($paginator)
            ->select($fieldNames, null, null, null, $sourceName, $ttl)
            ->getRows();
    }

    /**
     * Return collection of current model class name
     *
     * @param array $paginator
     * @param string $fieldNames
     * @param null $sourceName
     * @param int $ttl
     * @return Model_Collection
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.0
     */
    public static function getCollection(array $paginator, $fieldNames = '*', $sourceName = null, $ttl = 3600)
    {
        return self::query()
            ->setPaginator($paginator)
            ->select($fieldNames)
            ->getCollection($sourceName, $ttl);
    }

    /**
     * Return empty collection
     *
     * @return Model_Collection
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.1
     * @since 0.1
     */
    public static function getEmptyCollection()
    {
        return Model_Collection::create(new Query_Result([Query_Result::RESULT_MODEL_CLASS => self::getClass()]));
    }

    /**
     * Return localized title of table
     *
     * @param null $tableName
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public static function getTitle($tableName = null)
    {
        if (empty($tableName)) {
            $tableName = self::getTableName();
        }

        return self::getResource()->get($tableName);
    }

    /**
     * Return table name of self model class
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public static function getTableName()
    {
        $modelClass = self::getClass();
        return Data_Scheme::getInstance($modelClass::getScheme()->getScheme())->getModelClasses()[$modelClass];
    }

    /**
     * Return localized title of field
     *
     * @param $fieldName
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
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
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public static function getFieldPlaceholder($fieldName)
    {
        return self::getResource()->get($fieldName . '_placeholder');
    }

    /**
     * Return form of self model class
     *
     * @param array $filterFields
     * @return Form_Model
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public static function getForm(array $filterFields = [])
    {
        return Form_Model::getInstance(self::getClass())->addFilterFields($filterFields);
    }

    /**
     * Return data of self model class
     *
     * @param array $filterFields
     * @return Data_Model
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.0
     */
    public static function getData(array $filterFields = [])
    {
        return Data_Model::getInstance(self::getClass())->addFilterFields($filterFields);
    }

    /**
     * Return model by custom field
     *
     * @param $shortFieldName
     * @param $fieldValue
     * @param $fieldNames
     * @param null $sourceName
     * @param int $ttl
     * @return Model|null
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.0
     */
    public static function getModelBy($shortFieldName, $fieldValue, $fieldNames, $sourceName = null, $ttl = 3600)
    {
        return self::queryBy($shortFieldName, $fieldValue, $fieldNames)
            ->select($fieldNames, null, null, null, $sourceName, $ttl)
            ->getModel();
    }

    /**
     * Return Query builder by custom field
     *
     * @param $shortFieldName
     * @param $fieldValue
     * @return Query_Builder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.1
     */
    public static function getQueryBuilderBy($shortFieldName, $fieldValue)
    {
        return self::query()
            ->eq($shortFieldName, $fieldValue)
            ->limit(1);
    }

    /**
     * Return model by primary key
     *
     * @param $pk
     * @param array|string $fieldNames
     * @param string $sourceName
     * @param int $ttl
     * @return Model|null
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.0
     */
    public static function getModel($pk, $fieldNames, $sourceName = null, $ttl = 3600)
    {
        return self::query()
            ->pk($pk)
            ->limit(1)
            ->select($fieldNames, null, null, null, $sourceName, $ttl)
            ->getModel();
    }

    /**
     * Return all primary key names if them more then one
     *
     * @return mixed
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.1
     * @since 0.1
     */
    public static function getPkFieldNames()
    {
        $pkFieldNames = self::getRegistry()->get('pkFieldNames');

        if ($pkFieldNames) {
            return $pkFieldNames;
        }

        $fieldNames = array_flip(self::getMapping());

        return self::getRegistry()->set('pkFieldNames', array_map(
                function ($columnName) use ($fieldNames) {
                    return $fieldNames[$columnName];
                },
                self::getScheme()->getIndexes()['PRIMARY KEY']['PRIMARY']
            )
        );
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
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
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
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function __set($fieldName, $value)
    {
        return $this->set($fieldName, $value);
    }

//    /**
//     * Set data in model data
//     *
//     * @param $key
//     * @param null $value
//     *
//     * @author dp <denis.a.shestakov@gmail.com>
//     *
//     * @version 0.0
//     * @since 0.0
//     */
//    public function setData($key, $value = null)
//    {
//        if (is_array($key) && !$value) {
//            $this->_data = array_merge($this->_data, $key);
//            return;
//        }
//
//        $this->_data[$key] = $value;
//    }

    /**
     * Execute insert or update model data
     *
     * @param null $sourceName
     * @return Model|null
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.1
     * @since 0.0
     */
    public function insertOrUpdate($sourceName = null)
    {
        return $this->insert($sourceName, true);
    }

    /**
     * Execute insert into data source
     *
     * @param string $sourceName
     * @param bool $update
     * @return Model|null
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.0
     */
    public function insert($sourceName = null, $update = false)
    {
        /** @var Model $modelClass */
        $modelClass = get_class($this);

        $this->beforeInsert();

        $insertId = $modelClass::query()
            ->insert($this->_affected, $update, $sourceName)
            ->getInsertId();

        $this->_pk = reset($insertId);

        $this->afterInsert();

        $this->_affected = [];

        return $this;
    }

    /**
     * Run method before model insert
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.1
     * @since 0.1
     */
    protected function beforeInsert()
    {
    }

    /**
     * Run method after model insert
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.1
     * @since 0.1
     */
    protected function afterInsert()
    {
    }

    /**
     * Execute update for model
     *
     * @param $fieldName
     * @param null $value
     * @param null $sourceName
     * @return Model|null
     * @throws Exception
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function update($fieldName = null, $value = null, $sourceName = null)
    {
        if ($fieldName) {
            $this->set($fieldName, $value);
        }

        /** @var Model $modelClass */
        $modelClass = get_class($this);

        $this->beforeUpdate();

        $modelClass::query()->pk($this->getPk())->update($this->_affected, $sourceName);

        $this->afterUpdate();

        $this->_affected = [];

        return $this;
    }

    /**
     * Run method before model update
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.1
     * @since 0.1
     */
    protected function beforeUpdate()
    {
    }

    /**
     * Run method after model update
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.1
     * @since 0.1
     */
    protected function afterUpdate()
    {
    }

    /**
     * Execute delete for model
     *
     * @param null $sourceName
     * @return Model|null
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.0
     */
    public function delete($sourceName = null)
    {
        /** @var Model $modelClass */
        $modelClass = get_class($this);

        $this->beforeDelete();

        $modelClass::query()->delete($this->getPk(), $sourceName);

        $this->afterDelete();

        return $this;
    }

    /**
     * Run method before model delete
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.1
     * @since 0.1
     */
    protected function beforeDelete()
    {
    }

    /**
     * Run method after model delete
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.1
     * @since 0.1
     */
    protected function afterDelete()
    {
    }

    /**
     * Return array of extended fields by foreign keys
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getFk()
    {
        return $this->_fk;
    }

    /**
     * Return array of extended fields by json fields
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getJson()
    {
        return $this->_json;
    }

    /**
     * Return all rows for self model class
     *
     * @param array $pk
     * @param string $fieldNames
     * @param null $sourceName
     * @param int $ttl
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.2
     */
    public static function getRow($pk = [], $fieldNames = '*', $sourceName = null, $ttl = 3600)
    {
        return self::query()
            ->pk($pk)
            ->select($fieldNames, null, null, null, $sourceName, $ttl)
            ->getRow($pk);
    }

    /**
     * Casts model to string
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
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
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.0
     */
    public function getLink($modelClass, $modelPk, $linkModelClass = null, $sourceName = null, $ttl = 3600)
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

        return $linkModelClass::query()
            ->eq(strtolower($selfClassName) . '__fk', $this->getPk())
            ->eq(strtolower($className) . '__fk', $modelPk)
            ->select('*')
            ->getModel($sourceName, $ttl);
    }
}