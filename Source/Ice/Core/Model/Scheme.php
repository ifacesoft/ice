<?php
/**
 * Ice core model scheme container class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

use Ice;
use Ice\Core;
use Ice\Exception\Config_Not_Found;
use Ice\Exception\Model_Scheme_Error;
use Ice\Form\Model as Form_Model;
use Ice\Helper\Arrays;
use Ice\Helper\Date;
use Ice\Helper\Object;
use Ice\Helper\String;

/**
 * Class Model_Scheme
 *
 * Core model scheme container class
 *
 * @see Ice\Core\Container
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Core
 *
 * @version 0.0
 * @since 0.0
 */
class Model_Scheme
{
    use Core;

    /**
     * Model scheme
     *
     * @var Config
     */
    private $_modelSchemeConfig = null;

    /**
     * Private constructor for model scheme
     *
     * @param $modelClass
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.0
     */
    private function __construct($modelClass)
    {
        try {
            $this->_modelSchemeConfig = Config::create($modelClass, null, true);
        } catch (Config_Not_Found $e) {
            $this->_modelSchemeConfig = Config::newConfig($modelClass, Config::getDefault(__CLASS__))->save();
        }
    }

    /**
     * Synchronization local model scheme with remote data source table scheme
     *
     * @param $dataSourceKey
     * @param $tableName
     * @param bool $force
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.0
     * @return $this
     */
    public function update($dataSourceKey, $tableName, $force = false)
    {
        try {
            $this->setDataSourceKey($dataSourceKey);
            $this->setTableName($tableName);
        } catch (Model_Scheme_Error $e) {
            Model_Scheme::getLogger()->warning('Fail update model scheme', __FILE__, __LINE__, $e);
            return $this;
        }

        $dataSource = Data_Source::getInstance($dataSourceKey);

        $dataSourceColumns = $dataSource->getColumns($tableName);

        $diffColumns = Arrays::diff($this->getColumnMapping(), $dataSourceColumns);

        if (empty($diffColumns['added']) && empty($diffColumns['deleted']) && !$force) {
            return $this;
        }

        $currentRevision = $this->getRevision();

        $this->_modelSchemeConfig->set('time', Date::get(), true);
        $this->_modelSchemeConfig->set('revision', Date::getRevision(), true);

        $columnNames = array_flip($this->getFieldMapping());

        $modelClass = $this->getModelClass();

        foreach ($diffColumns['deleted'] as $columnName => $column) {
            $this->_modelSchemeConfig->remove('fields/' . $columnNames[$columnName]);
        }

        $this->_modelSchemeConfig->set('indexes', $dataSource->getIndexes($this->getTableName()), true);

        $primaryKeys = $this->getPkColumnNames();
        $foreignKeys = $this->getFkColumnNames();

        foreach ($diffColumns['added'] as $columnName => $column) {
            $fieldName = strtolower($columnName);

            $column['columnName'] = $columnName;
            $column['is_primary'] = false;
            $column['is_foreign'] = false;

            if (in_array($columnName, $foreignKeys)) {
                $column['is_foreign'] = true;
                if (substr($fieldName, -4, 4) != '__fk') {
                    $fieldName = String::trim($fieldName, ['__id', '_id', 'id'], String::TRIM_TYPE_RIGHT) . '__fk';
                }
            } else if (in_array($columnName, $primaryKeys)) {
                $column['is_primary'] = true;
                if (substr($fieldName, -3, 3) != '_pk') {
                    $fieldName = strtolower(Object::getName($modelClass));
                    do { // some primary fields
                        $fieldName .= '_pk';
                    } while (isset($modelMapping[$fieldName]));
                }
            }

            $fieldType = isset(Form_Model::$typeMap[$column['dataType']]) ? Form_Model::$typeMap[$column['dataType']] : 'text';

            $validators = [];

            switch ($fieldType) {
                case 'text':
                case 'textarea':
                    $validators['Ice:Length_Max'] = (int)$column['length'];
                    break;
                default:
            }

            if ($column['nullable'] === false && !$column['is_primary']) {
                $validators[] = 'Ice:Not_Null';
            }

            $this->_modelSchemeConfig->set('fields/' . $fieldName . '/' . __CLASS__, $column);
            $this->_modelSchemeConfig->set('fields/' . $fieldName . '/' . Data::getClass(), 'text');
            $this->_modelSchemeConfig->set('fields/' . $fieldName . '/' . Form::getClass(), $fieldType);
            $this->_modelSchemeConfig->set('fields/' . $fieldName . '/' . Validator::getClass(), $validators);
        }

        $this->_modelSchemeConfig->backup($currentRevision);
        $this->_modelSchemeConfig->save();

        Model::getCodeGenerator()->generate([$modelClass, array_keys($this->getFieldMapping())]);

        Model_Scheme::getLogger()->info(['Update scheme for model: {$0}', $modelClass], Logger::SUCCESS, true);

        return $this;

    }

    /**
     * Create new instance of model scheme
     *
     * @param $modelClass
     * @return Model_Scheme
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.0
     */
    public static function create($modelClass)
    {
        $dataProvider = Model_Scheme::getDataProvider();

        if ($modelScheme = $dataProvider->get($modelClass)) {
            return $modelScheme;
        }

        return $dataProvider->set($modelClass, new Model_Scheme($modelClass));
    }

    public function getFieldScheme($fieldName) {
        return $this->getFields()[$fieldName][__CLASS__];
    }

    /**
     * Return columns with their column schemes
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.0
     */
    public function getColumnMapping()
    {
        $repository = Model_Scheme::getRepository();
        $key = $this->getModelClass() . '/columns';
        if ($columns = $repository->get($key)) {
            return $columns;
        }

        $columns = [];

        foreach ($this->_modelSchemeConfig->gets('fields') as $fieldName => $fieldScheme) {
            $columns[$fieldScheme[__CLASS__]['columnName']] = $fieldName;
        }

        return $repository->set($key, $columns);
    }

    /**
     * Return indexes
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.0
     */
    public function getIndexes()
    {
        return $this->_modelSchemeConfig->gets('indexes');
    }

    /**
     * Return primary key columns
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    public function getPkColumnNames()
    {
        return $this->getIndexes()['PRIMARY KEY']['PRIMARY'];
    }

    /**
     * Return model class
     *
     * @return Model
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.5
     */
    public function getModelClass()
    {
        return $this->_modelSchemeConfig->getConfigName();
    }

    /**
     * Return model scheme revision
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.5
     */
    public function getRevision()
    {
        return $this->_modelSchemeConfig->get('revision');
    }

    /**
     * Return table name
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.5
     */
    public function getTableName()
    {
        return $this->_modelSchemeConfig->get('tableName', false);
    }

    /**
     * Return data source name
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.5
     */
    public function getDataSourceKey()
    {
        return $this->_modelSchemeConfig->get('dataSourceKey', false);
    }

    /**
     * Set data source key
     *
     * @param $dataSourceKey
     * @throws Exception
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.5
     */
    private function setDataSourceKey($dataSourceKey)
    {
        $key = $this->getDataSourceKey();

        if (!$key) {
            $this->_modelSchemeConfig->set('dataSourceKey', $dataSourceKey);
            return;
        }

        if ($key != $dataSourceKey) {
            Model_Scheme::getLogger()->exception(
                [
                    'Scheme of model {$0} expected dataSourceKey "{$1}" but found {$2}',
                    [$this->getModelClass(), $key, $dataSourceKey]
                ],
                __FILE__, __LINE__, null, null, -1, 'Ice:Model_Scheme_Error'
            );
        }
    }

    /**
     * Set table name
     *
     * @param $tableName
     * @throws Exception
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.5
     */
    private function setTableName($tableName)
    {
        $name = $this->getTableName();

        if (!$name) {
            $this->_modelSchemeConfig->set('tableName', $tableName);
            return;
        }

        if ($name != $tableName) {
            Model_Scheme::getLogger()->exception(
                [
                    'Scheme of model {$0} expected tableName "{$1}" but found {$2}',
                    [$this->getModelClass(), $name, $tableName]
                ],
                __FILE__, __LINE__, null, null, -1, 'Ice:Model_Scheme_Error'
            );
        }
    }

    /**
     * Return model scheme field names with column names
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.5
     */
    public function getFieldMapping()
    {
        return array_flip($this->getColumnMapping());
    }

    /**
     * Return foreign key column names
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.5
     */
    public function getFkColumnNames()
    {
        return Arrays::column($this->getIndexes()['FOREIGN KEY'], 0, '');
    }

    public function getFields()
    {
        return $this->_modelSchemeConfig->gets('fields');
    }
}