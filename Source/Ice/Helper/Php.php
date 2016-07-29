<?php
/**
 * Ice helper php class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Helper;

/**
 * Class Php
 *
 * Helper for php
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Helper
 */
class Php
{
    const INTEGER = 'integer';
    const BOOLEAN = 'boolean';

    /**
     * Pretty formatting php data (array)
     *
     * @param  $var
     * @param  bool $withPhpTag
     * @return mixed|string
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public static function varToPhpString($var, $withPhpTag = true, $isPretty = true)
    {
        $string = $withPhpTag
            ? '<?php' . "\n" . 'return ' . var_export($var, true) . ';'
            : var_export($var, true) . ';';

        if (!$isPretty) {
            return $string;
        }

        $string = str_replace('array (', '[', $string);
        $string = str_replace('(array(', '([', $string);
        $string = str_replace('),', '],', $string);
        $string = str_replace(')],', ']),', $string);
        $string = str_replace(');', '];', $string);
        $string = preg_replace('/=>\s+\[/', '=> [', $string);
        $string = preg_replace('/=> \[\s+\]/', '=> []', $string);
        for ($i = 10; $i >= 1; $i--) {
            $string = str_replace("\n" . str_repeat(' ', $i * 2), "\n" . str_repeat("\t", $i), $string);
        }
        $string = str_replace("\t", '    ', $string);
        $string = str_replace('NULL', 'null', $string);
        return $string;
    }

    /**
     * Test passing by reference
     *
     * @param $var
     */
    public static function passingByReference(&$var)
    {
        $var = String::getRandomString();
    }

    /**
     * @param $type
     * @param $var
     * @return array|bool|float|int|object|string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.0
     * @since   1.0
     */
    public static function castTo($type, $var)
    {
        $varType = gettype($var);

        if ($varType == $type) {
            return $var;
        }

        switch ($type) {
            case 'integer':
                return (int)$var;
            case 'string':
                return (string)$var;
            case 'boolean':
                return (bool)$var;
            case 'array':
                return (array)$var;
            case 'object':
                return (object)$var;
            case 'float':
                return (float)$var;
            case 'double':
                return (double)$var;
            case 'real':
                return (real)$var;
        }

        return $var;
    }

    /**
     * @param $filePath
     * @return array
     *
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.0
     * @since   1.0
     */
    public static function getClassNamesFromFile($filePath)
    {
        $phpString = file_get_contents($filePath);
        $classNames = Php::getClassNamesFromPhpString($phpString);
        return $classNames;
    }

    /**
     * @param $php_code
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.0
     * @since   1.0
     */
    public static function getClassNamesFromPhpString($php_code)
    {
        $classNames = array();
        $tokens = token_get_all($php_code);
        $count = count($tokens);
        for ($i = 2; $i < $count; $i++) {
            if ($tokens[$i - 2][0] == T_CLASS
                && $tokens[$i - 1][0] == T_WHITESPACE
                && $tokens[$i][0] == T_STRING
            ) {
                $class_name = $tokens[$i][1];
                $classNames[] = $class_name;
            }
        }
        return $classNames;
    }


    /**
     * Make a string with _ characters to camelCase method name
     *
     * @param $name
     * @param null $prefix
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   1.1
     */
    public static function camelCaseMethodName($name, $prefix = null)
    {
        $name = ucwords(str_replace('_', ' ', $name));
        return $prefix ? $prefix . $name : lcfirst($name);
    }
}
