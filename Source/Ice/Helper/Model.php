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
use Ice\Core\Logger;
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
     * @return Core_Model
     * @throws Exception
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.1
     * @since 0.0
     */
    public static function getModelClassByTableName($tableName)
    {
        $moduleAlias = null;
        $tableNamePart = $tableName;

        foreach (Core_Config::getInstance(Core_Model::getClass())->gets('prefixes') as $prefix => $value) {
            $prefix .= '_';

            if (String::startsWith($tableName, $prefix)) {
                $moduleAlias = $value;
                $tableNamePart = substr($tableName, strlen($prefix));
                break;
            }
        }

        if (!$moduleAlias) {
            $moduleAlias = Module::getInstance()->getAlias();
        }

        $modelName = $moduleAlias . '\Model\\';

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
}