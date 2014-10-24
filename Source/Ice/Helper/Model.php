<?php
/**
 * Ice helper model class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Helper;

use Ice\Core\Config;
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
 * @version stable_0
 * @since stable_0
 */
class Model
{
    /**
     * Return model class by known table name
     *
     * @param $tableName
     * @return Core_Model
     * @throws Exception
     */
    public static function getModelClassByTableName($tableName)
    {
        $tableNameParts = explode('_', $tableName);
        $prefix = current($tableNameParts);

        $moduleName = Config::getInstance(Core_Model::getClass())->get('prefixes/' . $prefix, false);

        if (!$moduleName) {
            $moduleName = Module::getInstance()->getAlias();
            array_unshift($tableNameParts, $prefix);
            $prefix = null;
        }

        $namespace = $moduleName . '\Model\\';

        if ($prefix) {
            $namespace .= ucfirst(preg_replace('/[^a-z]/i', '', $prefix)) . '\\';
        }

        $modelName = '';
        while ($part = next($tableNameParts)) {
            $modelName .= '_' . ucfirst($part);
        };

        return $namespace . ltrim($modelName, '_');
    }

    /**
     * Return table prefix
     *
     * @param $tableName
     * @return string
     * @throws Exception
     */
    public static function getTablePrefix($tableName)
    {
        $prefix = strstr($tableName, '_', true);

        if (!Config::getInstance(Core_Model::getClass())->get('prefixes/' . $prefix, false)) {
            return '';
        }

        return $prefix;
    }
}