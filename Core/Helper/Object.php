<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 20.01.14
 * Time: 23:24
 */

namespace ice\core\helper;

class Object
{
    public static function getName($objectClass)
    {
        if (!strpos(ltrim($objectClass, '\\'), '\\')) {
            return $objectClass;
        }

        return substr($objectClass, strrpos($objectClass, '\\') + 1);
    }
} 