<?php
/**
 * Ice helper model class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Helper;

/**
 * Class Model
 *
 * Helper for models
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Helper
 *
 * @version 0.0
 * @since   0.0
 */
class Model
{
    //
    //    /**
    //     * Return table prefix
    //     *
    //     * @param $tableName
    //     * @return string
    //     * @throws Exception
    //     *
    //     * @author dp <denis.a.shestakov@gmail.com>
    //     *
    //     * @version 0.0
    //     * @since 0.0
    //     */
    //    public static function getTablePrefix($tableName)
    //    {
    //        $prefix = strstr($tableName, '_', true);
    //
    //        if (!Core_Config::getInstance(Core_Model::getClass())->get('prefixes/' . $prefix, false)) {
    //            return '';
    //        }
    //
    //        return $prefix;
    //    }

    public static function getFieldNameByColumnName($columnName, array $table, array $tablePrefixes)
    {
        $fieldName = strtolower($columnName);

        $primaryKeys = $table['indexes']['PRIMARY KEY']['PRIMARY'];
        $foreignKeys = Type_Array::column($table['indexes']['FOREIGN KEY'], 0, '');

        if (in_array($columnName, $foreignKeys)) {
            $column['is_foreign'] = true;
            if (substr($fieldName, -4, 4) != '__fk') {
                $fieldName = Type_String::trim($fieldName, ['__id', '_id', 'id'], Type_String::TRIM_TYPE_RIGHT) . '__fk';
            }
        } elseif (in_array($columnName, $primaryKeys)) {
            $column['is_primary'] = true;
            if (substr($fieldName, -3, 3) != '_pk') {
                $fieldName = str_replace($tablePrefixes, '', $table['scheme']['tableName']);
                do { // some primary fields
                    $fieldName .= '_pk';
                } while (isset($modelMapping[$fieldName]));
            }
        }

        return $fieldName;
    }

    public static function schemeColumnScheme($columnName, $table, $tablePrefixes)
    {
        $default = isset($table['columns'][$columnName]['scheme']['default'])
            ? $table['columns'][$columnName]['scheme']['default']
            : null;

        return [
            'default' => $default === null ? null : trim($default, '\'')
        ];
    }

    public static function schemeColumnOptions($columnName, $table, $tablePrefixes)
    {
       return [
            'name' => Model::getFieldNameByColumnName(
                $columnName,
                $table,
                $tablePrefixes
            )
        ];
    }
}
