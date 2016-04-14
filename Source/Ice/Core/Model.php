<?php
/**
 * Ice core model abstract class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

use Ice\Core;
use Ice\Helper\Arrays;
use Ice\Helper\Json;
use Ice\Helper\Model as Helper_Model;
use Ice\Helper\Object;
use Ice\Helper\Spatial;
use Ice\Widget\Model_Form;
use Ice\Widget\Model_Table;

/**
 * Class Model
 *
 * Core module abstract class
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
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
    private $pk = null;

    /**
     *  Model fields
     *
     * @var array
     */
    protected $row = [];

    /**
     * Extended fields json
     *
     * @var array
     */
    private $json = [];

    /**
     * Extended fields for geo data
     *
     * @todo оставлю здесь, чтобы не потерять https://github.com/mjaschen/phpgeo
     * и http://geocoder-php.org/Geocoder/
     *
     * @var array
     */
    private $geo = [];

    /**
     * Extended fields by primary key
     *
     * @var array
     */
    private $fk = [];

    /**
     * Raw data of model
     *
     * @var array
     */
    private $raw = [];

    /**
     * Affected fields
     *
     * @var array
     */
    private $affected = [];

    /**
     * Private constructor. Create model: Model::create()
     *
     * @param  array $row
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.0
     */
    private function __construct(array $row)
    {
        $this->raw = $row;

        /**
         * @var Model $modelClass
         */
        $modelClass = self::getClass();
        $lowercaseModelName = strtolower(self::getClassName());

        $modelScheme = $modelClass::getScheme();

        foreach ($modelScheme->getFieldColumnMap() as $fieldName => $columnName) {
            $this->row[$fieldName] = null;

            if (empty($row)) {
                continue;
            }

            if (array_key_exists($fieldName, $row)) {
                $this->set($fieldName, $row[$fieldName]);
                unset($this->raw[$fieldName]);
                continue;
            }

            $length = strlen($lowercaseModelName);
            if (strpos($fieldName, $lowercaseModelName) === 0) {
                $shortFieldName = '/' . substr($fieldName, $length + 1);
                if (array_key_exists($shortFieldName, $row)) {
                    $this->set($fieldName, $row[$shortFieldName]);
                    unset($this->raw[$shortFieldName]);
                    continue;
                }

                // for '/data' => 'token_data__json'
                foreach (['__json', '__fk', '_geo'] as $ext) {
                    $extFieldName = strstr($shortFieldName, $ext, true);
                    if ($extFieldName !== false && array_key_exists($extFieldName, $row)) {
                        $this->set($extFieldName, $row[$extFieldName]);
                        unset($this->raw[$extFieldName]);

                        continue 2;
                    }
                }
            }

            foreach (['__json', '__fk', '_geo'] as $ext) {
                $extFieldName = strstr($fieldName, $ext, true);
                if ($extFieldName !== false && array_key_exists($extFieldName, $row)) {
                    $this->set($extFieldName, $row[$extFieldName]);
                    unset($this->raw[$extFieldName]);

                    continue 2;
                }
            }
        }
    }

    /**
     * Get class of model
     *
     * @param Model $modelClass
     *  class of short class (for example: Ice:User -> /Ice/Model/Ice/User)
     *
     * @param bool $required
     * @return Model|string
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.1
     * @since   0.0
     */
    public static function getClass($modelClass = null, $required = true)
    {
        if (!$modelClass) {
            /** @var Model $modelClass */
            $modelClass = get_called_class();
        } else {
            $modelClass = Object::getClass(__CLASS__, $modelClass);
        }

        if (!Loader::load($modelClass, false)) {
            if ($required) {
                Logger::getInstance($modelClass)->exception(['Model class {$0} not found', $modelClass], __FILE__, __LINE__);
            }

            return $modelClass;
        }

        if (in_array('Ice\Core\Model\Factory_Delegate', class_implements($modelClass))) {
            $modelClass = get_parent_class($modelClass);

            return $modelClass::getClass();
        }

        return $modelClass;
    }

    /**
     * @return ModelScheme
     */
    public static function getScheme()
    {
        $repository = self::getRepository();
        $key = 'scheme';

        if ($scheme = $repository->get($key)) {
            return $scheme;
        }

        /**
         * @var Model $modelClass
         */
        $modelClass = self::getClass();

        $scheme = ModelScheme::create(
            $modelClass,
            array_merge_recursive($modelClass::config(), Config::getInstance($modelClass, null, false, -1)->gets())
        );

        return $repository->set($key, $scheme);
    }

    protected static function config()
    {
        return [];
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
     * @param  array|string $fieldName
     * @param  mixed $fieldValue
     * @param  bool $isAffected Save for update
     * @return Model
     * @throws Exception
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   0.0
     */
    public function set($fieldName, $fieldValue = null, $isAffected = true)
    {
        if (!$fieldName) {
            return $this;
        }

        if (is_array($fieldName)) {
            foreach ($fieldName as $key => $value) {
                $this->set($key, $value, $isAffected);
            }

            return $this;
        }

        if ($this->setModelValue($fieldName, $isAffected)) {
            return $this;
        }

        $fieldName = $this->getFieldName($fieldName);

        if (!$this->setPkValue($fieldName, $fieldValue, $isAffected) &&
            !$this->setValue($fieldName, $fieldValue, $isAffected) &&
            !$this->setJsonValue($fieldName, $fieldValue, $isAffected) &&
            !$this->setSpatialValue($fieldName, $fieldValue, $isAffected) &&
            !$this->setFkValue($fieldName, $fieldValue, $isAffected)
        ) {
            /**
             * @var Model $modelClass
             */
            $modelClass = get_class($this);

            Logger::getInstance(__CLASS__)->exception(
                [
                    'Could not set value: Field "{$0}" not found in Model "{$1}"',
                    [$fieldName, $modelClass::getClassName()]
                ],
                __FILE__,
                __LINE__,
                null,
                $fieldValue
            );
        }

        return $this;
    }

    private function setModelValue($model, $isAffected)
    {
        if (!($model instanceof Model)) {
            return null;
        }

        /**
         * @var Model $fieldModelClass
         */
        $fieldModelClass = get_class($model);

        $fieldName = strtolower($fieldModelClass::getClassName()) . '__fk';
        $fieldValue = $model->getPk();

        $this->set($fieldName, $fieldValue, $isAffected);

        return [$fieldName => $fieldValue];
    }

    /**
     * Get primary key of model
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function getPk()
    {
        return $this->pk;
    }

    /**
     * @return Query_Scope
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   1.1
     */
    public static function getQueryScope()
    {
        return Query_Scope::getInstance(self::getClass());
    }

    public function getPkValue()
    {
        return $this->getPk() ? implode('__', $this->getPk()) : null;
    }

    /**
     * Gets full model field name if send short name (for example: '/name' for model User -> user_name)
     *
     * @param  $fieldName
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public static function getFieldName($fieldName)
    {
        $fieldName = trim($fieldName);

        if ($fieldName[0] == '/') {
            $fieldName[0] = '_';

            $modelClass = self::getClass();

            return strtolower($modelClass::getClassName()) . (strlen($fieldName) === 1 ? '' : $fieldName);
        }

        return $fieldName;
    }

    private function setPkValue($fieldName, $fieldValue, $isAffected)
    {
        if (!$this->isPkName($fieldName)) {
            return false;
        }

        if ($fieldValue === null) {
            return true;
        }

        if ($isAffected) {
            $this->addAffected($fieldName, $fieldValue);
        }

        $this->pk[$fieldName] = $fieldValue;

        if ($this->isFieldName($fieldName)) {
            unset($this->row[$fieldName]);
        }

        return true;
    }

    /**
     * Check is primary key value
     *
     * @param  $fieldName
     * @return bool
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    private function isPkName($fieldName)
    {
        /**
         * @var Model $modelClass
         */
        $modelClass = get_class($this);

        $modelScheme = $modelClass::getScheme();

        $FieldColumnMapping = $modelScheme->getFieldColumnMap();

        if (!isset($FieldColumnMapping[$fieldName])) {
            return false;
        }

        return in_array($FieldColumnMapping[$fieldName], $modelScheme->getPkColumnNames());
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
     * @since   0.4
     */
    private function addAffected($fieldName, $fieldValue)
    {
        if ($fieldValue === null) {
            /**
             * @var Model $modelClass
             */
            $modelClass = get_class($this);

            $columnName = $modelClass::getScheme()->getFieldColumnMap()[$fieldName];

            if ($modelClass::getConfig()->get('columns/' . $columnName . '/scheme/default') !== null) {
                return;
            }
        }

        $this->affected[$fieldName] = $fieldValue;
    }

    /**
     * Get action config
     *
     * @return Config
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since   0.5
     */
    public static function getConfig()
    {
        return self::getScheme();
    }

    /**
     * Check for exists field name in model
     *
     * @param  $fieldName
     * @return bool
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
     */
    public function isFieldName($fieldName)
    {
        return array_key_exists($fieldName, $this->row);
    }

    private function setValue($fieldName, $fieldValue, $isAffected)
    {
        if (!$this->isFieldName($fieldName)) {
            return null;
        }
        if ($isAffected) {
            $this->addAffected($fieldName, $fieldValue);
        }

        $this->row[$fieldName] = $fieldValue;

        return [$fieldName => $fieldValue];
    }

    private function setJsonValue($fieldName, $fieldValue, $isAffected)
    {
        $jsonFieldName = $fieldName . '__json';

        if (!$this->isFieldName($jsonFieldName)) {
            return null;
        }

        if ($fieldValue === null) {
            $this->json[$fieldName] = [];

            $fieldValue = Json::encode($this->json[$fieldName]);

            $this->set($jsonFieldName, $fieldValue, $isAffected);

            return [$jsonFieldName => $fieldValue];
        }

        if (!is_array($fieldValue)) {
            Logger::getInstance(__CLASS__)
                ->exception(['Supported only arrays in json field in model {0}', get_class($this)], __FILE__, __LINE__);
        }

        $this->json[$fieldName] = [];

        foreach ($fieldValue as $key => $value) {
            if ($value instanceof Model) {
                $this->json[$fieldName][$value::getClass()] = $value->getPk();
            } else {
                $this->json[$fieldName][$key] = $value;
            }
        }

        $fieldValue = Json::encode($this->json[$fieldName]);

        $this->set($jsonFieldName, $fieldValue, $isAffected);

        return [$jsonFieldName => $fieldValue];
    }

    /**
     * Get value of model field
     *
     * @param  null $fieldName
     * @param  bool $isNotNull
     * @param null $defaultCallback  return default value via callback method or function
     * @return mixed
     * @throws Exception
     * @throws \Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function get($fieldName = null, $isNotNull = true, $defaultCallback = null)
    {
        if ($fieldName === null) {
            return array_merge((array)$this->pk, array_filter($this->row, function ($value) { return $value !== null; }));
        }

        if (is_array($fieldName)) {
            $fields = [];

            foreach ($fieldName as $name) {
                $fields[$name] = $this->get($name, $isNotNull);
            }

            return $fields;
        }

        $fieldName = $this->getFieldName($fieldName);

        if ($this->isPkName($fieldName)) {
            return $this->getPkValue();
        }

        /**
         * @var Model $modelClass
         */
        $modelClass = get_class($this);
        $modelName = $modelClass::getClassName();

        foreach (array($this->row, $this->json, $this->fk) as $fields) {
            if (array_key_exists($fieldName, $fields)) {
                if ($isNotNull && $fields[$fieldName] === null) {
                    Logger::getInstance(__CLASS__)->exception(
                        ['field "{$0}" of model "{$1}" is null', [$fieldName, $modelName]],
                        __FILE__,
                        __LINE__
                    );
                }
                return $fields[$fieldName];
            }
        }

        $jsonFieldName = $fieldName . '__json';
        if ($this->isFieldName($jsonFieldName)) {
            $json = Json::decode($this->row[$jsonFieldName]);

            if (empty($json)) {
                return [];
            }

            $this->json[$fieldName] = $json;
            return $this->json[$fieldName];
        }

        $geoFieldName = $fieldName . '__geo';
        if ($this->isFieldName($geoFieldName)) {
            $geo = Spatial::decode($this->row[$geoFieldName]);

            if (empty($geo)) {
                return [];
            }

            $this->geo[$fieldName] = $geo;
            return $this->geo[$fieldName];
        }

        /**
         * @var Model $fieldName
         */
        $fieldModelName = Model::getClass($fieldName, false);

        // one-to-many
        $foreignKeyName = strtolower(Object::getClassName($fieldModelName)) . '__fk';
        if ($this->isFieldName($foreignKeyName)) {
            $key = $this->row[$foreignKeyName];

            if (!$key) {
                Logger::getInstance(__CLASS__)->exception(
                    ['Model::__get: Foreign key is missing - {$0} in model {$1}', [$foreignKeyName, $modelName]],
                    __FILE__,
                    __LINE__
                );
            }

            $row = array_merge($this->raw, [strtolower(Object::getClassName($fieldModelName)) . '_pk' => $key]);
            $joinModel = $fieldModelName::create($row);

            if (!$joinModel) {
                Logger::getInstance(__CLASS__)->exception(
                    [
                        'Model::__get: Foreign key is missing - {$0} = "{$1}" in model {$2}',
                        [$foreignKeyName, $key, $modelName]
                    ],
                    __FILE__,
                    __LINE__
                );
            }

            $this->fk[$fieldName] = $joinModel;
            return $this->fk[$fieldName];
        }

//        // TODO: Пока лениво подгружаем
//        // many-to-one
//        $foreignKeyName = strtolower($modelName) . '__fk';
//        if (array_key_exists($foreignKeyName, $fieldName::getScheme()->getFieldColumnMap())) {
//            $this->fk[$fieldName] = $fieldName::getSelectQuery('*', [$foreignKeyName => $this->getPk()])
//                ->getModelCollection();
//
//            return $this->fk[$fieldName];
//        }

        if (isset($this->raw[$fieldName])) {
            return $this->raw[$fieldName];
        }

        Logger::getInstance(__CLASS__)->exception(
            ['Field {$0} not found in Model {$1}', [$fieldName, $modelName]],
            __FILE__,
            __LINE__
        );

        return null;
    }

    /**
     * Create model instance
     *
     * @param  array $row
     * @return Model
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public static function create(array $row = [])
    {
        /**
         * @var Model $modelClass
         */
        $modelClass = self::getClass();

//        if (isset(class_parents($modelClass)[Model_Factory::getClass()])) {
//            $modelClass = $modelClass . '_' . $row[$modelClass::getFieldName('/delegate_name')];
//        }

        return new $modelClass($row);
    }

    private function setSpatialValue($fieldName, $fieldValue, $isAffected)
    {
        $geoFieldName = $fieldName . '__geo';

        if (!$this->isFieldName($geoFieldName)) {
            return null;
        }

        if ($fieldValue == null) {
            $this->geo[$fieldName] = null;

            $this->set($geoFieldName, null, $isAffected);

            return [$geoFieldName => null];
        }

        $this->geo[$fieldName] = $fieldValue;

        $fieldValue = Spatial::encode($this->geo[$fieldName]);

        $this->set($geoFieldName, $fieldValue, $isAffected);

        return [$geoFieldName => $fieldValue];
    }

    private function setFkValue($fieldName, $fieldValue, $isAffected)
    {
        $fkFieldName = $fieldName . '__fk';

        if (!$this->isFieldName($fkFieldName)) {
            return null;
        }

        if ($fieldValue == null) {
            $this->fk[$fieldName] = null;

            $this->set($fkFieldName, null, $isAffected);

            return [$fkFieldName => null];
        }

        $this->fk[$fieldName] = $fieldValue;
        /**
         * @var Model $fieldValue
         */
        $pk = $fieldValue->getPk();

        $fieldValue = reset($pk);

        $this->set($fkFieldName, $fieldValue, $isAffected);

        return [$fkFieldName => $fieldValue];
    }

    /**
     * @param string $selectFields
     * @param array $filterFields
     * @param array $pagination
     * @param null $dataSourceKey
     *
     * @todo add order param
     *
     * @return Query
     */
    public static function getSelectQuery($selectFields, array $filterFields = [], array $pagination = null, $dataSourceKey = null)
    {
        if (!$pagination) {
            $pagination = ['page' => 1, 'limit' => 0];
        }

        return Query::getBuilder(self::getClass())
            ->eq($filterFields)
            ->setPagination($pagination['page'], $pagination['limit'])
            ->getSelectQuery($selectFields, [], $dataSourceKey);
    }

    /**
     * Return localized title of table
     *
     * @param  null $tableName
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public static function getTitle($tableName = null)
    {
        if (empty($tableName)) {
            $tableName = self::getTableName();
        }

        return Resource::create(self::getClass())->get($tableName);
    }

    /**
     * Return table name of self model class
     *
     * @return string
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since   0.0
     */
    public static function getTableName()
    {
        return self::getConfig()->get('scheme/tableName');
    }

    /**
     * Return scheme name of self model class
     *
     * @return string
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   1.1
     */
    public static function getSchemeName()
    {
        $dataSourceKey = self::getConfig()->get('dataSourceKey');
        return substr($dataSourceKey, strpos($dataSourceKey, '.') + 1);
    }

    public static function getTablePrefix()
    {
        return Helper_Model::getTablePrefix(self::getTableName());
    }

    /**
     * Return localized title of placeholder field
     *
     * @param  $fieldName
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public static function getFieldPlaceholder($fieldName)
    {
        return Resource::create(self::getClass())->get($fieldName . '_placeholder');
    }

    /**
     * Return model validate scheme
     *
     * @param  $pluginClass
     * @return array
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since   0.0
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
     * Return localized title of field
     *
     * @param  $fieldName
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public static function getFieldTitle($fieldName)
    {
        return Resource::create(self::getClass())->get($fieldName);
    }

    /**
     * Return model by primary key
     *
     * @param  $pk
     * @param  array|string $fieldNames
     * @param  string|null $dataSourceKey
     * @param  int $ttl
     * @return Model|null
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   0.0
     */
    public static function getModel($pk, $fieldNames, $dataSourceKey = null, $ttl = null)
    {
        if (!$pk) {
            return null;
        }

        $modelClass = self::getClass();

        return $modelClass::getSelectQuery($fieldNames, ['/pk' => $pk], ['page' => 1, 'limit' => 1], $dataSourceKey)
            ->getModel(null, $ttl);
    }

    /**
     * Return all rows for self model class
     *
     * @param  $pk
     * @param  string $fieldNames
     * @param  string|null $dataSourceKey
     * @param  int $ttl
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   0.2
     */
    public static function getRow($pk, $fieldNames, $dataSourceKey = null, $ttl = null)
    {
        if (!$pk) {
            return null;
        }

        $modelClass = self::getClass();

        return $modelClass::getSelectQuery($fieldNames, ['/pk' => $pk], ['page' => 1, 'limit' => 1], $dataSourceKey)
            ->getRow(null, $ttl);
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
     * @since   0.3
     */
    public static function getPkFieldName()
    {
        $modelClass = self::getClass();

        return implode('__', $modelClass::getScheme()->getPkFieldNames());
    }

    public static function getFkFieldName()
    {
        $modelClass = self::getClass();

        return strtolower($modelClass::getClassName()) . '__fk';
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
     * @since   0.6
     */
    public static function getPkColumnName()
    {
        $modelClass = self::getClass();

        return implode('__', $modelClass::getScheme()->getPkColumnNames());
    }

    /**
     * Create table by model
     *
     * @param  string|null $dataSourceKey
     * @return QueryResult
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.2
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
     * @param  string|null $dataSourceKey
     * @return QueryResult
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.2
     */
    public static function dropTable($dataSourceKey = null)
    {
        return Query::getBuilder(self::getClass())->dropTableQuery($dataSourceKey)->getQueryResult();
    }

    /**
     * @param null $tableAlias
     * @return QueryBuilder
     */
    public static function createQueryBuilder($tableAlias = null)
    {
        return Query::getBuilder(self::getClass(), $tableAlias);
    }

    /**
     * Get dataSource for current model class
     *
     * @return DataSource
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   0.0
     */
    public static function getDataSource()
    {
        /** @var Model $modelClass */
        $modelClass = self::getClass();
        $parentModelName = get_parent_class($modelClass);

        if ($parentModelName == Model_Defined::getClass() || $parentModelName == Model_Factory::getClass()) {
            return DataSource::getInstance(Object::getClassName($parentModelName) . ':model/' . $modelClass);
        }

        return DataSource::getInstance($modelClass::getDataSourceKey());
    }

    /**
     * Return data source key
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since   0.5
     */
    public static function getDataSourceKey()
    {
        if ($dataSourceKey = self::getConfig()->get('dataSourceKey', false)) {
            return $dataSourceKey;
        }

        return $dataSourceKey = Module::getInstance()->getDataSourceKeys()[0];
    }

    /**
     * Magic get
     *
     * @deprecated use ->get($fieldName)
     *
     * @see Model::get()
     *
     * @param  $fieldName
     * @return mixed
     * @throws Exception
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
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
     * @param  $fieldName
     * @param  $value
     * @return Model
     * @throws Exception
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function __set($fieldName, $value)
    {
        return $this->set($fieldName, $value);
    }

    /**
     * Insert or update model
     *
     * @param  string|null $dataSourceKey
     * @param  bool $isSmart
     * @return Model
     * @throws Exception
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
     */
    public function save($isSmart = false, $dataSourceKey = null)
    {
        /**@var Model $modelClass */
        $modelClass = get_class($this);

        $pk = $this->getPk();
        $affected = $this->getAffected();

        $isSetPk = !empty($pk);
        // && $pk != array_intersect_key($affected, array_flip($modelClass::getScheme()->getPkFieldNames()));

        if (!$isSmart) {
            if ($isSetPk) {
                $this->update($modelClass, $affected, $dataSourceKey);
            } else {
                $this->insert($modelClass, $affected, false, $dataSourceKey);
            }

            return $this->clearAffected();
        }

        /** @var ModelScheme $modelScheme */
        $modelScheme = $modelClass::getScheme();

        $this->insert(
            $modelClass,
            array_merge(
                (array)$this->getPk(),
                array_intersect_key($this->get(), array_flip($modelScheme->getUniqueFieldNames())),
                $affected
            ),
            true,
            $dataSourceKey
        );

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
     * @since   0.4
     */
    private function getAffected()
    {
        return $this->affected;
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
            ->getUpdateQuery($affected, $dataSourceKey)
            ->getQueryResult();

        $this->afterUpdate();
    }

    /**
     * Run method before model update
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.1
     * @since   0.1
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
     * @since   0.1
     */
    protected function afterUpdate()
    {
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
            ->getInsertQuery($affected, $isSmart, $dataSourceKey)
            ->getQueryResult()
            ->getInsertId();

        if ($isSmart && $model = $modelClass::create(array_filter($this->row, function ($value) {
                return $value !== null;
            }))->find('/pk')
        ) {

            $this->set($model->getPk());
        }

        if ($this->pk === null) {
            $this->set(reset($insertId));
        }

        $this->afterInsert();
    }

    /**
     * Run method before model insert
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.1
     * @since   0.1
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
     * @since   0.1
     */
    protected function afterInsert()
    {
    }

    /**
     * Clear all affected values and return current model
     *
     * @return Model
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
     */
    public function clearAffected()
    {
        $this->affected = [];
        return $this;
    }

    /**
     * Execute select from data source
     *
     * @param  $fieldNames
     * @param  string|null $dataSourceKey
     * @param  int $ttl
     * @return Model|null
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since   0.2
     */
    public function find($fieldNames, $dataSourceKey = null, $ttl = null)
    {
        /**
         * @var Model $modelClass
         */
        $modelClass = get_class($this);

        $affected = $this->getAffected();

        $selectFields = array_merge($modelClass::getScheme()->getFieldNames($fieldNames), array_keys($affected));

        $row = $modelClass::getSelectQuery(
            $selectFields,
            empty($this->getPk()) ? $affected : array_merge($this->getPk(), $affected),
            ['page' => 1, 'limit' => 1],
            $dataSourceKey
        )
            ->getRow(null, $ttl);

        if (!$row) {
            return null;
        }

        $this->set($row);

        return $this->clearAffected();
    }

    /**
     * Execute delete for model
     *
     * @param  string|null $dataSourceKey
     * @return Model|null
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @bug удаление модели с множественным ключем -> пишет delete from 0 where pk in (?,?,?)
     * @version 0.2
     * @since   0.0
     */
    public function remove($dataSourceKey = null)
    {
        $this->beforeDelete();

        Query::getBuilder(get_class($this))->getDeleteQuery($this->getPk(), $dataSourceKey)->getQueryResult();

        $this->afterDelete();

        return $this;
    }

    /**
     * Run method before model delete
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.1
     * @since   0.1
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
     * @since   0.1
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
     * @since   0.0
     */
    public function getFk()
    {
        return $this->fk;
    }

    /**
     * Return array of extended fields by json fields
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function getJson()
    {
        return $this->json;
    }

    /**
     * Return link of model
     *
     * @param  Model $modelClass
     * @param  mixed $modelPk
     * @param  Model|null $linkModelClass
     * @param  string|null $dataSourceKey
     * @param  int $ttl
     * @return Model|null
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since   0.0
     */
    public function getLink($modelClass, $modelPk, $linkModelClass = null, $dataSourceKey = null, $ttl = null)
    {
        /**
         * @var Model $selfClass
         */
        $selfClass = get_class($this);

        $selfClassName = $selfClass::getClassName();
        $className = $modelClass::getClassName();

        if (!$linkModelClass) {
            $namespace = $selfClass::getNamespace();

            $modelClasses = Data_Scheme::getInstance()->getModelClasses();

            /**
             * @var Model $linkModelClass
             */
            $linkModelClass = isset($modelClasses[$namespace . $selfClassName . '_' . $className . '_Link'])
                ? $namespace . $selfClassName . '_' . $className . '_Link'
                : $namespace . $className . '_' . $selfClassName . '_Link';
        }

        return $linkModelClass::getSelectQuery(
            '*',
            [
                strtolower($selfClassName) . '__fk' => $this->getPk(),
                strtolower($className) . '__fk' => $modelPk
            ],
            ['page' => 1, 'limit' => 1],
            $dataSourceKey
        )->getModel(null, $ttl);
    }

    /**
     * Get query builder by current model
     *
     * @param  $modelClass
     * @param  null $tableAlias
     * @return QueryBuilder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since   0.5
     */
    public function getQueryBuilder($modelClass = null, $tableAlias = null)
    {
        $selfModelClass = get_class($this);

        return $modelClass
            ? Query::getBuilder($modelClass, $tableAlias)->inner($selfModelClass)->pk($this->getPk(), $selfModelClass)
            : Query::getBuilder($selfModelClass)->pk($this->getPk());
    }

    public function fetchOne($modelClass, $fieldNames, $lazy = false)
    {
        $modelClass = Model::getClass($modelClass);
        $modelName = $modelClass::getClassName();

        $field = strtolower($modelName);

        $fieldFk = $field . '__fk';

        if (!isset($this->row[$fieldFk])) {
            return null;
        }

        $raw = empty($this->fk[$fieldFk])
            ? $this->raw
            : array_merge($this->raw, $this->fk[$fieldFk]);

        return $lazy
            ? $modelClass::getModel($this->row[$fieldFk], $fieldNames)
            : $modelClass::create($raw);
    }

    /**
     * @param Model $modelClass
     * @param $fieldNames
     * @param bool|false $lazy
     * @return mixed
     */
    public function fetchMany($modelClass, $fieldNames, $lazy = false)
    {
        /** @var Model $selfModelClass */
        $selfModelClass = get_class($this);
        $selfModelName = $selfModelClass::getClassName();

        $modelScheme = $selfModelClass::getScheme();

        $modelClass = Model::getClass($modelClass);
        $modelName = $modelClass::getClassName();
        $modelClassFieldColumnMap = $modelClass::getScheme()->getFieldColumnMap();

//        foreach ($modelScheme->gets('relations/manyToOne') as $relationClass => $relationField) {
//            if ($relationClass == $modelClass) {
//                return $modelClass::createQueryBuilder()
//                    ->inner($selfModelClass, null, $selfModelName . '.')
//            }
//        }

        /**
         * @var Model $relationClass
         * @var Model $relationClassLink
         */
        foreach ($modelScheme->gets('relations/manyToMany') as $relationClass => $relationClassLink) {
            if ($relationClass == $modelClass) {
                $relationClassLink = reset($relationClassLink);

                $relationClassLinkName = $relationClassLink::getClassName();

                $relationClassLinkFieldColumnMap = $relationClassLink::getScheme()->getFieldColumnMap();

                return $modelClass::createQueryBuilder()
                    ->inner($relationClassLink, '/pk', $relationClassLinkName . '.' .
                        $relationClassLinkFieldColumnMap[strtolower($modelName) . '__fk'] . '=' . $modelName . '.' .
                        $modelClassFieldColumnMap[strtolower($modelName) . '_pk'] . ' AND ' . $relationClassLinkName . '.' .
                        $relationClassLinkFieldColumnMap[strtolower($selfModelName) . '__fk'] . '=' . $this->getPkValue())
                    ->getSelectQuery($fieldNames)
                    ->getModelCollection();
            }
        }

        Logger::getInstance(__CLASS__)->exception(
            ['Fetch many of {$0} for {$1} failed. Relations not found.', [$modelClass, $selfModelClass]],
            __FILE__,
            __LINE__
        );

        return null;
    }

    public function getModelCollection($fieldNames, $filter)
    {

    }

    public static function getResource()
    {
        return Resource::create(self::getClass());
    }

    /**
     * @param null $fieldName
     * @return mixed
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   1.1
     */
    public function getRaw($fieldName = null)
    {
        return $fieldName === null ? $this->raw : $this->raw[$fieldName];
    }
}
