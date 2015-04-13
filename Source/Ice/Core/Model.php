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
use Ice\Ui_Data\Model as Data_Model;
use Ice\Form\Model as Ui_Form_Model;
use Ice\Helper\Json;
use Ice\Helper\Model as Helper_Model;
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
     * @todo оставлю здесь, чтобы не потерять https://github.com/mjaschen/phpgeo и http://geocoder-php.org/Geocoder/
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
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.0
     */
    private function __construct(array $row)
    {
        /** @var Model $modelClass */
        $modelClass = self::getClass();
        $lowercaseModelName = strtolower(self::getClassName());

        $modelScheme = $modelClass::getScheme();

        foreach ($modelScheme->getFieldColumnMap() as $fieldName => $columnName) {
            $this->_row[$fieldName] = null;

            if (empty($row)) {
                continue;
            }

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
        }

        $this->_data = $row;
    }

    /**
     * Get class of model
     *
     * @param Model $modelClass
     *  class of short class (for example: Ice:User -> /Ice/Model/Ice/User)
     *
     * @return string|Model
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

        if (!Loader::load($modelClass, false)) {
            Model::getLogger()->exception(['Model class {$0} not found', $modelClass], __FILE__, __LINE__);
        }

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
     * Method set value of model field
     *
     * example usage:
     * ```php
     *  $user->set('/name', 'Guest'); // set value 'Guest' for field 'user_name'
     *  $user->set(['user_name' => 'Name', '/surname' => 'Surname']); // sets array params
     *  $user->set('data', ['data1' => 'string1', 'data2' => 'string2']); // set value of field data__json
     * ```
     *
     * @param array|string $fieldName
     * @param mixed $fieldValue
     * @param bool $isAffected Save for update
     * @return Model
     * @throws Exception
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
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
                $this->addAffected($fieldName, $fieldValue);
            }

            $this->_pk[$fieldName] = $fieldValue;
            if ($this->isFieldName($fieldName)) {
                unset($this->_row[$fieldName]);
            }

            return $this;
        }
        if ($this->isFieldName($fieldName)) {
            if ($isAffected) {
                $this->addAffected($fieldName, $fieldValue);
            }

            $this->_row[$fieldName] = $fieldValue;
            return $this;
        }

        $jsonFieldName = $fieldName . '__json';
        if ($this->isFieldName($jsonFieldName)) {
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

            $sets = $this->set(
                $jsonFieldName,
                Json::encode(array_merge($this->get($fieldName), $values)),
                $isAffected
            );

            $this->_json[$fieldName] = $values;

            return $sets;
        }

        $geoFieldName = $fieldName . '__geo';
        if ($this->isFieldName($geoFieldName)) {
            if ($fieldValue == null) {
                $this->_geo[$fieldName] = null;
                return $this->set($geoFieldName, null, $isAffected);
            }

            $this->_geo[$fieldName] = $fieldValue;
            $this->set($geoFieldName, Spatial::encode($this->_geo[$fieldName]), $isAffected);
        }

        $fkFieldName = $fieldName . '__fk';
        if ($this->isFieldName($fkFieldName)) {
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

        Model::getLogger()->exception(
            [
                'Could not set value: Field "{$0}" not found in Model "{$1}"',
                [$fieldName, $modelClass::getClassName()]
            ],
            __FILE__, __LINE__, null, $fieldValue
        );

        return $this;
    }

    /**
     * Get primary key of model
     *
     * @return array
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

        $modelScheme = $modelClass::getScheme();

        $FieldColumnMapping = $modelScheme->getFieldColumnMap();

        if (!isset($FieldColumnMapping[$fieldName])) {
            return false;
        }

        return in_array($FieldColumnMapping[$fieldName], $modelScheme->getPkColumnNames());
    }

    /**
     * @return Model_Scheme
     */
    public static function getScheme()
    {
        $repository = self::getRepository();
        $key = 'scheme';
        if ($scheme = $repository->get($key)) {
            return $scheme;
        }

        /** @var Model $modelClass */
        $modelClass = self::getClass();

        $scheme = Model_Scheme::create($modelClass, array_merge_recursive($modelClass::config(), Config::getInstance($modelClass, null, false, -1)->gets()));

        return $repository->set($key, $scheme);
    }

    /**
     * Get dataSource for current model class
     *
     * @return Data_Source
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.0
     */
    public function getDataSource()
    {
        /** @var Model $modelClass */
        $modelClass = get_class($this);
        $parentModelName = get_parent_class($modelClass);

        if ($parentModelName == Model_Defined::getClass() || $parentModelName == Model_Factory::getClass()) {
            return Data_Source::getInstance(Object::getName($parentModelName) . ':model/' . $modelClass);
        }

        return Data_Source::getInstance($modelClass::getDataSourceKey());
    }

    /**
     * Return data source key
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.5
     */
    public static function getDataSourceKey()
    {
        return self::getConfig()->get('dataSourceKey');
    }

    /**
     * Add affected field value
     *
     * @param $fieldName
     * @param $fieldValue
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    private function addAffected($fieldName, $fieldValue)
    {
        if ($fieldValue === null) {
            /** @var Model $modelClass */
            $modelClass = get_class($this);

            $columnName = $modelClass::getScheme()->getFieldColumnMap()[$fieldName];

            if ($modelClass::getConfig()->get('columns/' . $columnName . '/scheme/default') !== null) {
                return;
            }
        }

        $this->_affected[$fieldName] = $fieldValue;
    }

    /**
     * Check for exists field name in model
     *
     * @param $fieldName
     * @return bool
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    public function isFieldName($fieldName)
    {
        return array_key_exists($fieldName, $this->_row);
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
            return array_merge((array)$this->_pk, $this->_row);
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
                    Model::getLogger()->exception(['field "{$0}" of model "{$1}" is null', [$fieldName, $modelName]], __FILE__, __LINE__);
                }
                return $fields[$fieldName];
            }
        }

        $jsonFieldName = $fieldName . '__json';
        if ($this->isFieldName($jsonFieldName)) {
            $json = Json::decode($this->_row[$jsonFieldName]);

            if (empty($json)) {
                return [];
            }

            $this->_json[$fieldName] = $json;
            return $this->_json[$fieldName];
        }

        $geoFieldName = $fieldName . '__geo';
        if ($this->isFieldName($geoFieldName)) {
            $geo = Spatial::decode($this->_row[$geoFieldName]);

            if (empty($geo)) {
                return [];
            }

            $this->_geo[$fieldName] = $geo;
            return $this->_geo[$fieldName];
        }

        try {
            /** @var Model $fieldName */
            $fieldName = Model::getClass($fieldName);

            // one-to-many
            $foreignKeyName = strtolower(Object::getName($fieldName)) . '__fk';
            if ($this->isFieldName($foreignKeyName)) {
                $key = $this->_row[$foreignKeyName];

                if (!$key) {
                    Model::getLogger()->exception(['Model::__get: Не определен внешний ключ {$0} в модели {$1}', [$foreignKeyName, $modelName]], __FILE__, __LINE__);
                }

                $row = array_merge($this->_data, [strtolower(Object::getName($fieldName)) . '_pk' => $key]);
                $joinModel = $fieldName::create($row);

                if (!$joinModel) {
                    Model::getLogger()->exception(
                        [
                            'Model::__get: Не удалось получить модель по внешнему ключу {$0} = "{$1}" в модели {$2}',
                            [$foreignKeyName, $key, $modelName]
                        ],
                        __FILE__, __LINE__
                    );
                }

                $this->_fk[$fieldName] = $joinModel;
                return $this->_fk[$fieldName];
            }

            // TODO: Пока лениво подгружаем
            // many-to-one
            $foreignKeyName = strtolower($modelName) . '__fk';
            if (array_key_exists($foreignKeyName, $fieldName::getScheme()->getFieldColumnMap())) {
                $this->_fk[$fieldName] = Query::getBuilder($fieldName)
                    ->eq([$foreignKeyName => $this->getPk()])
                    ->select('*')
                    ->getModelCollection();

                return $this->_fk[$fieldName];
            }
        } catch (\Exception $e) {
            // $fieldName not model // todo: refactoring
        }

        Model::getLogger()->exception(['Field {$0} not found in Model {$1}', [$fieldName, $modelName]], __FILE__, __LINE__);
        return null;
    }

    /**
     * Create model instance
     *
     * @param array $row
     * @return Model
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public static function create(array $row = [])
    {
        /** @var Model $modelClass */
        $modelClass = get_called_class();

        if (isset(class_parents($modelClass)[Model_Factory::getClass()])) {
            $modelClass = $modelClass . '_' . $row[$modelClass::getFieldName('/delegate_name')];
        }

        return new $modelClass($row);
    }

    /**
     * Return model validate scheme
     *
     * @param $pluginClass
     * @return array
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.0
     */
    public static function getPlugin($pluginClass)
    {
        $repository = self::getRepository('plugins');

        if ($data = $repository->get($pluginClass)) {
            return $data;
        }

        $data = [];

        foreach (self::getConfig()->gets('columns') as $column) {
            $data[$column['fieldName']] = $column[$pluginClass];
        }

        return $repository->set($pluginClass, $data);
    }

    /**
     * Return all rows for self model class
     *
     * @param array $pagination
     * @param string $fieldNames
     * @param string|null $dataSourceKey
     * @param int $ttl
     * @return array
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.0
     */
    public static function getRows(array $pagination, $fieldNames = '*', $dataSourceKey = null, $ttl = null)
    {
        return Query::getBuilder(self::getClass())
            ->setPagination($pagination)
            ->select($fieldNames, null, [], $dataSourceKey, $ttl)
            ->getRows();
    }

    /**
     * Return collection of current model class name
     *
     * @param array $pagination
     * @param string $fieldNames
     * @param string|null $dataSourceKey
     * @param int $ttl
     * @return Model_Collection
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.0
     */
    public static function getCollection($fieldNames, array $pagination = [1, 1000, 0], $dataSourceKey = null, $ttl = null)
    {
        return Query::getBuilder(self::getClass())
            ->setPagination($pagination)
            ->select($fieldNames, null, [], $dataSourceKey, $ttl)
            ->getModelCollection();
    }

    protected static function config()
    {
        return [];
    }

    /**
     * Get action config
     *
     * @return Config
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.5
     */
    public static function getConfig()
    {
        return self::getScheme();
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
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.0
     */
    public static function getTableName()
    {
        return self::getConfig()->get('scheme/tableName');
    }

    public static function getTablePrefix()
    {
        return Helper_Model::getTablePrefix(self::getTableName());
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
     * @return Ui_Form_Model
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public static function getForm(array $filterFields = [])
    {
        return Ui_Form_Model::getInstance(self::getClass())->addFilterFields($filterFields);
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
     * @param array $fieldValueNames
     * @param $fieldNames
     * @param string|null $dataSourceKey
     * @param int $ttl
     * @return Model|null
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.0
     */
    public static function getModelBy(array $fieldValueNames, $fieldNames, $dataSourceKey = null, $ttl = null)
    {
        return Query::getBuilder(self::getClass())
            ->eq($fieldValueNames)
            ->limit(1)
            ->select($fieldNames, [], $dataSourceKey)
            ->getModel($ttl);
    }

    /**
     * Return model by primary key
     *
     * @param $pk
     * @param array|string $fieldNames
     * @param string|null $dataSourceKey
     * @param int $ttl
     * @return Model|null
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.0
     */
    public static function getModel($pk, $fieldNames, $dataSourceKey = null, $ttl = null)
    {
        return Query::getBuilder(self::getClass())
            ->pk($pk)
            ->limit(1)
            ->select($fieldNames, null, [], $dataSourceKey)
            ->getModel(null, $ttl);
    }

    /**
     * Return all rows for self model class
     *
     * @param array $pk
     * @param string $fieldNames
     * @param string|null $dataSourceKey
     * @param int $ttl
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.2
     */
    public static function getRow($pk = [], $fieldNames = '*', $dataSourceKey = null, $ttl = null)
    {
        return Query::getBuilder(self::getClass())
            ->pk($pk)
            ->select($fieldNames, null, [], $dataSourceKey, $ttl)
            ->getRow($pk);
    }

    /**
     * Return primary key field value
     *
     * If have a some primary keys - return key1__key2__key3__etc
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.3
     * @since 0.3
     */
    public static function getPkFieldName()
    {
        $modelClass = self::getClass();

        return implode('__', $modelClass::getScheme()->getPkFieldNames());
    }

    /**
     * Return primary key field value
     *
     * If have a some primary keys - return key1__key2__key3__etc
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.6
     */
    public static function getPkColumnName()
    {
        $modelClass = self::getClass();

        return implode('__', $modelClass::getScheme()->getPkColumnNames());
    }

    /**
     * Create table by model
     *
     * @param string|null $dataSourceKey
     * @return Query_Result
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.2
     */
    public static function createTable($dataSourceKey = null)
    {
        return Query::getBuilder(self::getClass())
            ->createTableQuery($dataSourceKey)
            ->getQueryResult();
    }

    /**
     * Drop table by model
     *
     * @param string|null $dataSourceKey
     * @return Query_Result
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.2
     */
    public static function dropTable($dataSourceKey = null)
    {
        return Query::getBuilder(self::getClass())->dropTableQuery($dataSourceKey)->getQueryResult();
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

    /**
     * Execute select from data source
     *
     * @param $fieldNames
     * @param string|null $dataSourceKey
     * @param int $ttl
     * @return Model|null
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.2
     */
    public function find($fieldNames, $dataSourceKey = null, $ttl = null)
    {
        /** @var Model $modelClass */
        $modelClass = get_class($this);

        $affected = $this->getAffected();

        $selectFields = array_merge($modelClass::getScheme()->getFieldNames($fieldNames), array_keys($affected));

        $row = Query::getBuilder($modelClass)
            ->eq($affected)
            ->select($selectFields, [], $dataSourceKey)
            ->getRow($ttl);

        if (!$row) {
            return null;
        }

        $this->set($row);

        return $this->clearAffected();
    }

    /**
     * Return affected fields
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    private function getAffected()
    {
        return $this->_affected;
    }

    /**
     * Clear all affected values and return current model
     *
     * @return Model
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    public function clearAffected()
    {
        $this->_affected = [];
        return $this;
    }

    /**
     * @param Model $modelClass
     * @param $affected
     * @param $isSmart
     * @param $dataSourceKey
     */
    private function insert($modelClass, $affected, $isSmart, $dataSourceKey)
    {
        $this->beforeInsert();

        $insertId = Query::getBuilder($modelClass)
            ->insertQuery($affected, $isSmart, $dataSourceKey)
            ->getQueryResult()
            ->getInsertId();

        $this->set(reset($insertId));

        $this->afterInsert();
    }

    /**
     * @param Model $modelClass
     * @param $affected
     * @param $dataSourceKey
     */
    private function update($modelClass, $affected, $dataSourceKey)
    {
        $this->beforeUpdate();

        Query::getBuilder($modelClass)
            ->pk($this->getPk())
            ->updateQuery($affected, $dataSourceKey)
            ->getQueryResult();

        $this->afterUpdate();
    }

    /**
     * Insert or update model
     *
     * @param string|null $dataSourceKey
     * @param bool $isSmart
     * @return Model
     * @throws Exception
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    public function save($isSmart = false, $dataSourceKey = null)
    {
        /** @var Model $modelClass */
        $modelClass = get_class($this);

        $pk = $this->getPk();
        $affected = $this->getAffected();

        $isSetPk = !empty($pk) && $pk == array_intersect_key($affected, array_flip($modelClass::getScheme()->getPkFieldNames()));

        if (!$isSmart) {
            if ($isSetPk) {
                $this->update($modelClass, $affected, $dataSourceKey);
            } else {
                $this->insert($modelClass, $affected, false, $dataSourceKey);
            }

            return $this->clearAffected();
        }

        if (!$isSetPk) {
            if ($this->find('/pk')) {
                return $this;
            }

            $this->insert($modelClass, $affected, false, $dataSourceKey);
            return $this->clearAffected();
        }

        $this->insert($modelClass, $affected, true, $dataSourceKey);
        return $this->clearAffected();
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
     * @param string|null $dataSourceKey
     * @return Model|null
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.0
     */
    public function remove($dataSourceKey = null)
    {
        $this->beforeDelete();

        Query::getBuilder(get_class($this))->deleteQuery($this->getPk(), $dataSourceKey)->getQueryResult();

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
     * @param string|null $dataSourceKey
     * @param int $ttl
     * @return Model|null
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.0
     */
    public function getLink($modelClass, $modelPk, $linkModelClass = null, $dataSourceKey = null, $ttl = null)
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

        return Query::getBuilder($linkModelClass)
            ->eq([
                strtolower($selfClassName) . '__fk' => $this->getPk(),
                strtolower($className) . '__fk' => $modelPk
            ])
            ->select('*', null, $dataSourceKey)
            ->getModel();
    }

    /**
     * @return Query_Builder
     */
    public static function createQueryBuilder() {
        return Query::getBuilder(self::getClass());
    }

    /**
     * Get query builder by current model
     *
     * @param $modelClass
     * @param null $tableAlias
     * @return Query_Builder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.5
     */
    public function getQueryBuilder($modelClass = null, $tableAlias = null)
    {
        $selfModelClass = get_class($this);

        return $modelClass
            ? Query::getBuilder($modelClass, $tableAlias)
                ->inner($selfModelClass)
                ->pk($this->getPk(), $selfModelClass)
            : Query::getBuilder($selfModelClass)
                ->pk($this->getPk());
    }
}