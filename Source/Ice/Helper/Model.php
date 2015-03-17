<?php
/**
 * Ice helper model class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Helper;

use Ice\Core\Config as Core_Config;
use Ice\Core\Exception;
use Ice\Core\Model as Core_Model;
use Ice\Core\Module;

/**
 * Class Model
 *
 * Helper for models
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Helper
 *
 * @version 0.0
 * @since 0.0
 */
class Model
{
    /**
     * Return model class by known table name
     *
     * @param $tableName
     * @param null $moduleAlias
     * @return Core_Model
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.0
     */
    public static function getModelClassByTableName($tableName, $moduleAlias = null)
    {
        $alias = null;
        $tableNamePart = $tableName;

        foreach (Module::getInstance($moduleAlias)->getTablePrefixes() as $prefix => $value) {
            if (String::startsWith($tableName, $prefix)) {
                $alias = $value;
                $tableNamePart = substr($tableName, strlen($prefix));
                break;
            }
        }

        if (!$alias) {
            $alias = Module::getInstance()->getAlias();
        }

        $modelName = $alias . '\Model\\';

        foreach (explode('_', preg_replace('/_{2,}/', '_', $tableNamePart)) as $modelNamePart) {
            $modelName .= ucfirst($modelNamePart) . '_';
        }

        return rtrim($modelName, '_');
    }

    /**
     * Return table prefix
     *
     * @param $tableName
     * @return string
     * @throws Exception
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public static function getTablePrefix($tableName)
    {
        $prefix = strstr($tableName, '_', true);

        if (!Core_Config::getInstance(Core_Model::getClass())->get('prefixes/' . $prefix, false)) {
            return '';
        }

        return $prefix;
    }

    /**
     * Return field name by column name
     *
     * @param $columnName
     * @param $table
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.5
     */
    public static function getFieldNameByColumnName($columnName, $table)
    {
        $fieldName = strtolower($columnName);

        $primaryKeys = $table['indexes']['PRIMARY KEY']['PRIMARY'];
        $foreignKeys = Arrays::column($table['indexes']['FOREIGN KEY'], 0, '');

        if (in_array($columnName, $foreignKeys)) {
            $column['is_foreign'] = true;
            if (substr($fieldName, -4, 4) != '__fk') {
                $fieldName = String::trim($fieldName, ['__id', '_id', 'id'], String::TRIM_TYPE_RIGHT) . '__fk';
            }
        } else if (in_array($columnName, $primaryKeys)) {
            $column['is_primary'] = true;
            if (substr($fieldName, -3, 3) != '_pk') {
                $fieldName = strtolower(Object::getName(Model::getModelClassByTableName($table['scheme']['tableName'])));
                do { // some primary fields
                    $fieldName .= '_pk';
                } while (isset($modelMapping[$fieldName]));
            }
        }

        return $fieldName;
    }
}