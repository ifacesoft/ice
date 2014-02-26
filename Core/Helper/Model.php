<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 21.02.14
 * Time: 0:36
 */

namespace ice\core\helper;

class Model
{
    public static function tableToModel($tableName)
    {
        $name = self::getShortTableNameByTableName($tableName);

        $modelNameParts = explode('_', $name);
        foreach ($modelNameParts as &$part) {
            $part[0] = strtoupper($part[0]);
        }

        return implode('_', $modelNameParts);
    }

    public static function getShortTableNameByTableName($tableName)
    {
        return substr($tableName, strlen(strstr($tableName, '_', true) . '_'), strlen($tableName));
    }

} 