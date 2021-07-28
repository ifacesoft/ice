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
use Ice\Exception\Error;
use Ice\Helper\Class_Object;
use Ice\Helper\Json;
use Ice\Helper\Model as Helper_Model;
use Ice\Helper\Spatial;
use Ice\Path;
use Ifacesoft\Ice\Core\Domain\Value\StringValue;
use Ifacesoft\Ice\Core\Domain\Value\ValueObject;

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
    use Path;

    private static $cacheSchemes;

    /**
     *  Model fields
     *
     * @var array
     */
    protected $row = [];
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
     * @param array $row
     * @throws Exception
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
        $length = strlen($lowercaseModelName);

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
            $modelClass = Class_Object::getClass(__CLASS__, $modelClass);
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
     * @throws Exception
     */
    public static function getScheme()
    {
        $modelClass = self::getClass();

        if (isset(Model::$cacheSchemes[$modelClass])) {
            return Model::$cacheSchemes[$modelClass];
        }

        return Model::$cacheSchemes[$modelClass] = ModelScheme::getInstance($modelClass, null, false, -1, $modelClass::config());
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
     * @param array|string $fieldName
     * @param mixed $fieldValue
     * @param bool $isAffected InfoSave for update
     * @return Model
     * @throws Exception
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.2
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

        $affected = array_merge(
            $this->setValue($fieldName, $fieldValue),
            $this->setJsonValue($fieldName, $fieldValue),
            $this->setSpatialValue($fieldName, $fieldValue),
            $this->setFkValue($fieldName, $fieldValue)

        );

        if ($affected) {
            if ($isAffected) {
                $this->addAffected($affected);
            }
        } else {
            $this->raw[$fieldName] = $fieldValue;
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
     * @param array $pk
     *
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.2
     * @since   1.2
     */
    public function setPk(array $pk)
    {
        $this->pk = $pk;
    }

    /**
     * Gets full model field name if send short name (for example: '/name' for model User -> user_name)
     *
     * @param  $fieldName
     * @param null $tableAlias
     * @return string
     *
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public static function getFieldName($fieldName, $tableAlias = null)
    {
        $fieldName = trim($fieldName);

        if ($fieldName[0] === '/') {
            $fieldName[0] = '_';

            if (!$tableAlias) {
                $modelClass = self::getClass();

                $tableAlias = $modelClass::getClassName();
            }

            return strtolower($tableAlias) . (strlen($fieldName) === 1 ? '' : $fieldName);
        }

        return $fieldName;
    }

    /**
     * @param $fieldName
     * @param $fieldValue
     * @return array
     *
     * @throws Exception
     * @version 1.2
     * @since   1.0
     * @author dp <denis.a.shestakov@gmail.com>
     *
     */
    private function setValue($fieldName, $fieldValue)
    {
        if ($this->isPkName($fieldName)) {
            if ($this->isFieldName($fieldName)) {
                unset($this->row[$fieldName]);
            }

            if ($fieldValue === null) {
                $this->pk = null;

                return [];
            } else {
                $this->pk[$fieldName] = $fieldValue;
            }
        } elseif ($this->isFieldName($fieldName)) {
            $this->row[$fieldName] = $fieldValue;
        } else {
            return [];
        }

        return [$fieldName => $fieldValue];
    }

    /**
     * Check is primary key value
     *
     * @param  $fieldName
     * @return bool
     *
     * @throws Exception
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
     * Check for exists field name in model
     *
     * @param  $fieldName
     * @return bool
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.2
     * @since   0.4
     */
    public function isFieldName($fieldName)
    {
        return array_key_exists($fieldName, $this->row);
    }

    /**
     * @param $fieldName
     * @param $fieldValue
     * @return array
     *
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.2
     * @since   1.0
     */
    private function setJsonValue($fieldName, $fieldValue)
    {
        $jsonFieldName = $fieldName . '__json';

        if (!$this->isFieldName($jsonFieldName)) {
            return [];
        }

        if (!is_array($fieldValue)) {
            $fieldValue = ((array)$fieldValue);
        }

        $this->json[$fieldName] = [];

        foreach ($fieldValue as $key => $value) {
            if ($value instanceof Model) {
                $this->json[$fieldName][$value::getClass()] = $value->getPk();
            } else {
                $this->json[$fieldName][$key] = $value;
            }
        }

        return $this->setValue($jsonFieldName, Json::encode($this->json[$fieldName]));
    }

    private function setSpatialValue($fieldName, $fieldValue)
    {
        $geoFieldName = $fieldName . '__geo';

        if (!$this->isFieldName($geoFieldName)) {
            return [];
        }

        if (!$fieldValue) {
            $fieldValue = null;
        }

        $this->geo[$fieldName] = $fieldValue;

        if ($fieldValue) {
            $fieldValue = Spatial::encode($this->geo[$fieldName]);
        }

        return $this->setValue($geoFieldName, $fieldValue);
    }

    private function setFkValue($fieldName, $fieldValue)
    {
        $fkFieldName = $fieldName . '__fk';

        if (!$this->isFieldName($fkFieldName)) {
            return [];
        }

        if (!($fieldValue instanceof Model)) {
            $fieldValue = null;
        }

        if ($fieldValue) {
            $pk = $fieldValue->getPk();

            $this->fk[get_class($fieldValue)] = $fieldValue;

            $fieldValue = reset($pk);
        }

        return $this->setValue($fkFieldName, $fieldValue);
    }

    /**
     * Add affected field value
     *
     * @param array $fields
     *
     * @throws Error
     * @throws Exception
     * @throws \Ice\Exception\Config_Error
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.2
     * @since   0.4
     */
    private function addAffected(array $fields)
    {
        foreach ($fields as $fieldName => $fieldValue) {

            if ($fieldValue === null) {
                /**
                 * @var Model $modelClass
                 */
                $modelClass = get_class($this);

                $columnName = $modelClass::getScheme()->getFieldColumnMap()[$fieldName];
                $columnScheme = $modelClass::getConfig()->gets('columns/' . $columnName . '/scheme');

                if (!$columnScheme['nullable']) {
                    if ($columnScheme['default'] === null) {
                        throw new Error(['Null value not allowed for field {$0} of model {$1}', [$fieldName, $modelClass]]);
                    }

                    $fieldValue = $columnScheme['default'];
                }
            }

            $this->affected[$fieldName] = $fieldValue;
        }
    }

    /**
     * Get action config
     *
     * @return Config
     *
     * @throws Exception
     * @version 0.5
     * @since   0.5
     * @author dp <denis.a.shestakov@gmail.com>
     *
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
     * @throws Exception
     * @throws \Ice\Exception\FileNotFound
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
     * @throws Exception
     * @version 0.5
     * @since   0.0
     * @author dp <denis.a.shestakov@gmail.com>
     *
     */
    public static function getTableName()
    {
        return self::getConfig()->get('scheme/tableName');
    }

    /**
     * Return scheme name of self model class
     *
     * @return string
     * @throws Exception
     * @version 1.1
     * @since   1.1
     * @author dp <denis.a.shestakov@gmail.com>
     *
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
     * @throws Exception
     * @throws \Ice\Exception\FileNotFound
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
     * @throws Exception
     * @throws \Ice\Exception\Config_Error
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
     * @throws Exception
     * @throws \Ice\Exception\FileNotFound
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
     * Return all rows for self model class
     *
     * @param  $pk
     * @param string $fieldNames
     * @param string|null $dataSourceKey
     * @param int $ttl
     * @return array
     *
     * @throws Exception
     * @version 1.1
     * @since   0.2
     * @author dp <denis.a.shestakov@gmail.com>
     *
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
     * @param string|array $selectFields
     * @param array $filterFields
     * @param array $pagination
     * @param null $dataSourceKey
     *
     * @param null $tableAlias
     * @return Query
     * @throws Exception
     * @todo add order param
     *
     */
    public static function getSelectQuery($selectFields, array $filterFields = [], array $pagination = null, $dataSourceKey = null, $tableAlias = null)
    {
        $pagination = (array)$pagination;


        if (!isset($pagination['page'])) {
            $pagination['page'] = 1;
        }

        if (!isset($pagination['limit'])) {
            $pagination['limit'] = 0;
        }

        return Query::getBuilder(self::getClass(), $tableAlias)
            ->eq($filterFields)
            ->setPagination($pagination['page'], $pagination['limit'])
            ->getSelectQuery($selectFields, [], $dataSourceKey);
    }

    /**
     * Return primary key field value
     *
     * If have a some primary keys - return key1__key2__key3__etc
     *
     * @return string
     *
     * @throws Exception
     * @version 0.3
     * @since   0.3
     * @author dp <denis.a.shestakov@gmail.com>
     *
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
     * @throws Exception
     * @version 0.6
     * @since   0.6
     * @author dp <denis.a.shestakov@gmail.com>
     *
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
     * @return QueryResult
     * @throws Exception
     * @throws \Ice\Exception\Config_Error
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
     * @param string|null $dataSourceKey
     * @return QueryResult
     * @throws Exception
     * @version 0.6
     * @since   0.2
     * @author dp <denis.a.shestakov@gmail.com>
     *
     */
    public static function dropTable($dataSourceKey = null)
    {
        return Query::getBuilder(self::getClass())->dropTableQuery($dataSourceKey)->getQueryResult();
    }

    /**
     * Get dataSource for current model class
     *
     * @return DataSource
     *
     * @throws Exception
     * @version 1.13
     * @since   0.0
     * @author dp <denis.a.shestakov@gmail.com>
     *
     */
    public static function getDataSource($dataSourceKey = null)
    {
        if ($dataSourceKey !== null) {
            return DataSource::getInstance($dataSourceKey);
        }

        /** @var Model $modelClass */
        $modelClass = self::getClass();
        $parentModelName = get_parent_class($modelClass);

        if ($parentModelName == Model_Defined::getClass() || $parentModelName == Model_Factory::getClass()) {
            return DataSource::getInstance(Class_Object::getClassName($parentModelName) . ':model/' . $modelClass);
        }

        return DataSource::getInstance($modelClass::getDataSourceKey());
    }

    /**
     * Return data source key
     *
     * @return string
     *
     * @throws Exception
     * @version 0.5
     * @since   0.5
     * @author dp <denis.a.shestakov@gmail.com>
     *
     */
    public static function getDataSourceKey()
    {
        if ($dataSourceKey = self::getConfig()->get('dataSourceKey', false)) {
            return $dataSourceKey;
        }

        return $dataSourceKey = Module::getInstance()->getDataSourceKeys()[0];
    }

    public static function getResource()
    {
        return Resource::create(self::getClass());
    }

    /**
     * Rows for ListBox like widget components
     *
     * @param string $itemKey
     * @param string $itemTitle
     *
     * @return array
     * @throws Exception
     * @since   1.1
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     */
    public static function getItems($itemKey = '/pk', $itemTitle = '/name')
    {
        $modelClass = self::getClass();

        return $modelClass::getSelectQuery([$itemKey, $itemTitle])->getRows();
    }

    /**
     * @param $data
     * @param $fieldNames
     * @return Model|null
     * @throws Exception
     * @todo: научить работать с несколькими уникальными полями в одном индексе
     */
    public static function getUniqueModel($data, $fieldNames)
    {
        /** @var Model $modelClass */
        $modelClass = self::getClass();

        $modelScheme = $modelClass::getScheme();

        $uniqueFieldNames = array_intersect_key($modelScheme->getColumnFieldMap(), array_flip($modelClass::getScheme()->getUniqueColumnNames()));

        $queryBuilder = $modelClass::createQueryBuilder();

        $count = 0;

        foreach ($data as $fieldName => $fieldValue) {
            $fieldName = $modelClass::getFieldName($fieldName);

            if (in_array($fieldName, $uniqueFieldNames)) {
                $queryBuilder->eq([$fieldName => $fieldValue], [], QueryBuilder::SQL_LOGICAL_OR);
                $count++;
            }
        }

        if (!$count) {
            return null;
        }

        return $queryBuilder->getSelectQuery($fieldNames)->getModel();
    }

    /**
     * @param null $tableAlias
     * @return QueryBuilder
     * @throws Exception
     */
    public static function createQueryBuilder($tableAlias = null)
    {
        return Query::getBuilder(self::getClass(), $tableAlias);
    }

    public function setPkValue($pkValue)
    {
        /** @var Model $modelClass */
        $modelClass = get_class($this);

        $this->setPk(array_combine($modelClass::getScheme()->getPkFieldNames(), explode('__', $pkValue)));

        return $this;
    }

    /**
     * Magic get
     *
     * @param  $fieldName
     * @return mixed
     * @throws Exception
     *
     * @see Model::get()
     *
     * @deprecated use ->get($fieldName)
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
     * @param  $fieldName
     * @param  $value
     * @return Model
     * @throws Exception
     *
     * @deprecated use ->set($fieldName, $value)
     *
     * @see Model::set()
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
     * Get value of model field
     *
     * @param null $fieldName
     * @param null $default
     * @return mixed
     * @throws Exception
     * @throws \Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   0.0
     */
    public function get($fieldName = null, $default = null)
    {
        if ($fieldName === null) {
            return array_merge((array)$this->pk, array_filter($this->row, function ($value) {
                return $value !== null;
            }));
        }

        if (is_array($fieldName)) {
            $fields = [];

            foreach ($fieldName as $name => $alias) {
                if (is_int($name)) {
                    $name = $alias;
                }

                $fields[$alias] = $this->get($name, $default);
            }

            return $fields;
        }

        $fieldName = $this->getFieldName($fieldName);

        if ($this->isPkName($fieldName) && $this->pk[$fieldName] !== null) {
            return $this->pk[$fieldName];
        }

        /**
         * @var Model $modelClass
         */
        $modelClass = get_class($this);
        $modelName = $modelClass::getClassName();

        foreach (array($this->row, $this->json, $this->fk) as $fields) {
            if (array_key_exists($fieldName, $fields)) {
                if ($fields[$fieldName] === null || $fields[$fieldName] === '') {
                    if ($default !== null) {
                        return $default;
                    }

                    $modelScheme = $modelClass::getScheme();
                    $fieldColumnMap = $modelScheme->getFieldColumnMap();

                    $columnScheme = $modelScheme->gets('columns/' . $fieldColumnMap[$fieldName] . '/scheme');

                    $default = $columnScheme['default'];

                    if ($default !== null) {
                        return $default;
                    }

                    if ($columnScheme['nullable']) {
                        return $fields[$fieldName];
                    }

                    Logger::getInstance(__CLASS__)->exception(
                        ['field "{$0}" of model "{$1}" is null', [$fieldName, $modelClass]],
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
                if ($default !== null) {
                    return $default;
                }

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
        $foreignKeyName = strtolower(Class_Object::getClassName($fieldModelName)) . '__fk';
        if ($this->isFieldName($foreignKeyName)) {
            $key = $this->row[$foreignKeyName];

            if (!$key) {
                Logger::getInstance(__CLASS__)->exception(
                    ['Model::__get: Foreign key is missing - {$0} in model {$1}', [$foreignKeyName, $modelName]],
                    __FILE__,
                    __LINE__
                );
            }

            $row = array_merge($this->raw, [strtolower(Class_Object::getClassName($fieldModelName)) . '_pk' => $key]);
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

        throw new Error(['Field {$0} not found in Model {$1}', [$fieldName, $modelName]]);
    }

    /**
     * Create model instance
     *
     * @param array $row
     * @return $this
     *
     * @throws Exception
     * @version 0.0
     * @since   0.0
     * @author dp <denis.a.shestakov@gmail.com>
     *
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

    /**
     * Insert or update model
     *
     * @param string|null $dataSourceKey
     * @param bool $isSmart
     * @return $this
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

        $isSetPk = !empty($pk);
        // && $pk != array_intersect_key($affected, array_flip($modelClass::getScheme()->getPkFieldNames()));

        if (!$isSmart) {
            if ($isSetPk) {
                $this->update($modelClass, $dataSourceKey);
            } else {
                $this->insert($modelClass, false, $dataSourceKey);
            }

            return $this->clearAffected();
        }

        $modelScheme = $modelClass::getScheme();

        $this->affected = array_merge(
            (array)$this->getPk(),
            array_intersect_key($this->get(), array_flip($modelScheme->getUniqueFieldNames())),
            $this->affected
        );

        $this->insert($modelClass, true, $dataSourceKey);

        return $this->clearAffected();
    }

    /**
     * @param Model $modelClass
     * @param $dataSourceKey
     * @throws Exception
     */
    private function update($modelClass, $dataSourceKey)
    {
        $this->beforeUpdate();

        if ($affected = $this->getAffected()) {
            Query::getBuilder($modelClass)
                ->pk($this->getPk())
                ->getUpdateQuery($affected, $dataSourceKey)
                ->getQueryResult();
        }

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
     * Return affected fields
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
     */
    protected function getAffected()
    {
        return $this->affected;
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
     * @param $isSmart
     * @param $dataSourceKey
     * @throws Exception
     */
    private function insert($modelClass, $isSmart, $dataSourceKey)
    {
        $this->beforeInsert();

        $affected = $this->getAffected();

        $insertId = Query::getBuilder($modelClass)
            ->getInsertQuery($affected, $isSmart, $dataSourceKey)
            ->getQueryResult()
            ->getInsertId();

        if ($this->pk === null) {
            $this->set(reset($insertId));
        }

        if ($affected) {
            if ($isSmart) {
                $model = $modelClass::create(array_filter($this->get(), function ($value) {
                    return $value !== null;
                }));
                if ($model) {
                    $uniqueFieldNames = ModelScheme::getInstance(get_class($model))->getUniqueFieldNames();

                    $model->find($uniqueFieldNames); // bug: Need use getUniqueIndex  => WHERE ('PRIMARY' = id) OR (first_unique = first AND second_unique = second)

                    $this->set($model->get($uniqueFieldNames));
                }
            }
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
     * Execute select from data source
     *
     * @param  $fieldNames
     * @param string|null $dataSourceKey
     * @param int $ttl
     * @param bool $return
     * @return Model|null
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since   0.2
     * @todo Не делать запрос, если данные уже загружены
     */
    public function find($fieldNames, $dataSourceKey = null, $ttl = null, $return = true)
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
        )->getRow(null, $ttl);

        if (!$row) {
            return $return ? $this : null;
        }

        $this->set($row);

        return $this->clearAffected();
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
     * Execute delete for model
     *
     * @param string|null $dataSourceKey
     * @return Model|null
     *
     * @throws \Exception
     * @version 0.2
     * @since   0.0
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @bug удаление модели с множественным ключем -> пишет delete from 0 where pk in (?,?,?)
     */
    public function remove($dataSourceKey = null)
    {
        $this->beforeDelete();

        $affectedRows = Query::getBuilder(get_class($this))
            ->getDeleteQuery($this->getPk(), $dataSourceKey)
            ->getQueryResult()
            ->getAffectedRows();

        if ($affectedRows) {
            $this->afterDelete();

            return $this;
        }

        return null;
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

//    /**
//     * Return link of model
//     *
//     * @param  Model $modelClass
//     * @param  mixed $modelPk
//     * @param  Model|null $linkModelClass
//     * @param  string|null $dataSourceKey
//     * @param  int $ttl
//     * @return Model|null
//     *
//     * @author dp <denis.a.shestakov@gmail.com>
//     *
//     * @version 0.2
//     * @since   0.0
//     */
//    public function getLink($modelClass, $modelPk, $linkModelClass = null, $dataSourceKey = null, $ttl = null)
//    {
//        /**
//         * @var Model $selfClass
//         */
//        $selfClass = get_class($this);
//
//        $selfClassName = $selfClass::getClassName();
//        $className = $modelClass::getClassName();
//
//        if (!$linkModelClass) {
//            $namespace = $selfClass::getNamespace();
//
//            $modelClasses = DataScheme::getInstance()->getModelClasses();
//
//            /**
//             * @var Model $linkModelClass
//             */
//            $linkModelClass = isset($modelClasses[$namespace . $selfClassName . '_' . $className . '_Link'])
//                ? $namespace . $selfClassName . '_' . $className . '_Link'
//                : $namespace . $className . '_' . $selfClassName . '_Link';
//        }
//
//        return $linkModelClass::getSelectQuery(
//            '*',
//            [
//                strtolower($selfClassName) . '__fk' => $this->getPk(),
//                strtolower($className) . '__fk' => $modelPk
//            ],
//            ['page' => 1, 'limit' => 1],
//            $dataSourceKey
//        )->getModel(null, $ttl);
//    }

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
     * Get query builder by current model
     *
     * @param  $modelClass
     * @param null $tableAlias
     * @return QueryBuilder
     *
     * @throws Exception
     * @version 0.5
     * @since   0.5
     * @author dp <denis.a.shestakov@gmail.com>
     *
     */
    public function getQueryBuilder($modelClass = null, $tableAlias = null)
    {
        $selfModelClass = get_class($this);

        return $modelClass
            ? Query::getBuilder($modelClass, $tableAlias)->inner($selfModelClass)->pk($this->getPk(), $selfModelClass)
            : Query::getBuilder($selfModelClass)->pk($this->getPk());
    }

    public function fetchOne($modelClass, $fieldNames, $lazy = false, $ttl = null, $dataSourceKey = null)
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
            ? $modelClass::getModel($this->row[$fieldFk], $fieldNames, $dataSourceKey, $ttl)
            : $modelClass::create($raw);
    }

    /**
     * Return model by primary key
     *
     * @param  $pk
     * @param array|string $fieldNames
     * @param string|null $dataSourceKey
     * @param int $ttl
     * @return $this
     * @throws Exception
     * @version 1.1
     * @since   0.0
     * @author dp <denis.a.shestakov@gmail.com>
     *
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
     * @param Model|string $modelClass
     * @param $fieldNames
     * @param bool|false $lazy
     * @return mixed
     * @throws Exception
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

    public function getPkValue()
    {
        return $this->getPk() ? implode('__', $this->getPk()) : null;
    }

    /**
     * @param null $fieldName
     * @param null $default
     * @return mixed
     * @throws Error
     * @throws \Ice\Exception\FileNotFound
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.13
     * @since   1.1
     */
    public function getRaw($fieldName = null, $default = null)
    {
        if (empty($fieldName)) {
            return $this->raw;
        }

        $required = func_num_args() === 1;

        if (is_array($fieldName)) {
            $default = (array)$default;
        }

        $raw = [];

        foreach ((array)$fieldName as $field) {
            if (array_key_exists($field, $this->raw)) {
                $raw[$field] = $this->raw[$field];
            } else {
                if ($required) {
                    throw new Error(['Required param {$0}', $fieldName], $this->raw);
                }

                $raw[$field] = is_array($default) && array_key_exists($field, $default) ? $default[$field] : $default;
            }
        }

        return is_array($default) ? $raw : reset($raw);
    }

    public function setRaw(array $data)
    {
        $this->raw = array_merge($this->raw, $data);

        return $this;
    }

    /**
     * @param Model $linkModelClass
     * @param $linkKeyName
     * @param $linkForeignKeyName
     * @param $keys
     * @return $this
     * @throws Exception
     */
    public function removeLinks($linkModelClass, $linkKeyName, $linkForeignKeyName, $keys = null)
    {
        if ($keys !== null && empty($keys)) {
            return $this;
        }

        $pkValue = $this->getPkValue();

        $links = [];

        foreach ($keys as $key) {
            $links[] = [
                $linkKeyName => $pkValue,
                $linkForeignKeyName => $key
            ];
        }

        // $linkModelClass::createQueryBuilder()->getDeleteQuery($links)->getQueryResult();

        foreach ($links as $link) {
            $linkModelClass::createQueryBuilder()
                ->eq($link)
                ->getDeleteQuery()
                ->getQueryResult();
        }

        return $this;
    }

    /**
     * @param Model $linkModelClass
     * @param $linkKeyName
     * @param $linkForeignKeyName
     * @param $keys
     * @return $this
     * @throws Exception
     */
    public function addLinks($linkModelClass, $linkKeyName, $linkForeignKeyName, $keys)
    {
        if (empty($keys)) {
            return $this;
        }

        $pkValue = $this->getPkValue();

        $links = [];

        foreach ($keys as $key) {
            $links[] = [
                $linkKeyName => $pkValue,
                $linkForeignKeyName => $key
            ];
        }

        $linkModelClass::createQueryBuilder()->getInsertQuery($links)->getQueryResult();

        return $this;
    }

    public function getLogger()
    {
        return Logger::getInstance(get_class($this));
    }

    public function checkAffected(array $fieldNames)
    {
        /** @var Model $modelClass */
        $modelClass = get_class($this);

        $affected = $this->getAffected();

        foreach ($fieldNames as $fieldName) {
            $fieldName = $modelClass::getFieldName($fieldName);

            if (array_key_exists($modelClass::getFieldName($fieldName), $affected)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $path
     * @param $fileName
     * @param bool $isCreate
     * @return string
     * @throws Error
     * @throws Exception
     */
    public function getCacheFile($path, $fileName, $isCreate = false)
    {
        $pk = $this->getPkValue();

        if (empty($pk)) {
            throw new Error('Primary key of model not defined. Cache dir not generated');
        }

        return $this->getModuleCacheFile(get_class($this) . '/' . $pk . '/' . $path, $fileName, $isCreate);
    }

    /**
     * @param $path
     * @param bool $isCreate
     * @return string
     * @throws Error
     * @throws Exception
     */
    public function getTempDir($path = '', $isCreate = false)
    {
        $pk = $this->getPkValue();

        if (empty($pk)) {
            throw new Error('Primary key of model not defined. Temp dir not generated');
        }

        return $this->getModuleTempDir(get_class($this) . '/' . $pk . '/' . $path, $isCreate);
    }

    /**
     * @param $path
     * @param $fileName
     * @param bool $isCreate
     * @return string
     * @throws Error
     * @throws Exception
     */
    public function getUploadFile($path, $fileName, $isCreate = false)
    {
        $pk = $this->getPkValue();

        if (empty($pk)) {
            throw new Error('Primary key of model not defined. Upload dir not generated');
        }

        return $this->getModuleUploadFile(get_class($this) . '/' . $pk . '/' . $path, $fileName, $isCreate);
    }

    /**
     * @param $path
     * @param bool $isCreate
     * @return string
     * @throws Error
     * @throws Exception
     */
    public function getUploadDir($path, $isCreate = false)
    {
        $pk = $this->getPkValue();

        if (empty($pk)) {
            throw new Error('Primary key of model not defined. Upload dir not generated');
        }

        return $this->getModuleUploadDir(get_class($this) . '/' . $pk . '/' . $path, $isCreate);
    }

    /**
     * @param $path
     * @param $fileName
     * @param bool $isCreate
     * @return string
     * @throws Error
     * @throws Exception
     */
    public function getDataFile($path, $fileName, $isCreate = false)
    {
        $pk = $this->getPkValue();

        if (empty($pk)) {
            throw new Error('Primary key of model not defined. Data dir not generated');
        }

        return $this->getModuleDataFile(get_class($this) . '/' . $pk . '/' . $path, $fileName, $isCreate);
    }

    public function is($fileName, $default = null)
    {
        return (bool)$this->get($fileName, $default);
    }

    /**
     * @param array $map
     * @return array|Model|mixed|string
     * @throws Exception
     */
    public function map(array $map)
    {
        /** @var Model $modelClass */
        $modelClass = get_class($this);

        $array = $this->get();

        $deleted = [];

        foreach ($map as $alias => $field) {
            $type = null;

            if (is_array($field)) {
                /** @var ValueObject $type */
                list($field, $type) = array_pad($field, 2, StringValue::class);
            }

            $field = $modelClass::getFieldName($field);

            if (is_int($alias)) {
                $alias = $field;
            }

            $array[$alias] = $this->get($field, '');

            if ($type) {
                $array[$alias] = $type::create($array[$alias])->getValue();
            }

            if ($field !== $alias) {
                $deleted[] = $field;
            }
        }

        foreach (array_unique($deleted) as $field) {
            unset($array[$field]);
        }

        return $array;
    }
}
