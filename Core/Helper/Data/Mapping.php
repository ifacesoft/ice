<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 20.01.14
 * Time: 23:21
 */

namespace ice\core\helper;


class Data_Mapping
{
    public static function getTableNameByClass($modelClass)
    {
        $table = strtolower(Object::getName($modelClass));
        $namespace = substr($modelClass, 0, strrpos($modelClass, '\\'));
        $prefix = substr($namespace, strrpos($namespace, '\\') + 1) . '_';
        return $prefix . $table;
    }
}