<?php
/**
 * Ice helper php class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Helper;

/**
 * Class Php
 *
 * Helper for php
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Helper
 */
class Php
{
    const INTEGER = 'integer';

    /**
     * Pretty formatting php data (array)
     *
     * @param $var
     * @return mixed|string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public static function varToPhpString($var)
    {
        $string = '<?php' . "\n" . 'return ' . var_export($var, true) . ';';
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

    public static function castTo($type, $var)
    {
        $varType = gettype($var);

        if ($varType == $type) {
            return $var;
        }

        switch ($type) {
            case 'integer':
                return (int) $var;
            case 'string':
                return (string) $var;
            case 'boolean':
                return (bool) $var;
            case 'array':
                return (array) $var;
            case 'object':
                return (object) $var;
            case 'float':
                return (float) $var;
            case 'double':
                return (double) $var;
            case 'real':
                return (real) $var;
        }

        return $var;
    }
}