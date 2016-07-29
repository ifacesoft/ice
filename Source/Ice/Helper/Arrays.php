<?php
/**
 * Ice helper arrays class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Helper;

use Ice\Core\Logger as Core_Logger;
use Ice\Exception\Error;

/**
 * Class Arrays
 *
 * Helper for arrays
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Helper
 *
 * @version 0.0
 * @since   0.0
 */
class Arrays
{
    /**
     * Filter array by filter scheme
     *
     * Удаляет целую строку
     *
     *  $filterScheme = [
     *      ['name', 'Petya', '='],
     *      ['age', 18, '>'],
     *      ['surname', 'Iv%', 'like']
     *  ];
     *
     * @param  array $rows
     * @param array $filterScheme
     * @return array
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   0.0
     */
    public static function filterRows(array $rows, $filterScheme = [['*', null, '<>']])
    {
        $filterFunction = function ($filterSchemes) {
            return function ($row) use ($filterSchemes) {
                if (!is_array($row)) {
                    throw new Error('Filter array must be multidimensional ');
                }

                foreach ($filterSchemes as $filterScheme) {
                    list($field, $value, $comparison) = $filterScheme;
                    $field = $field !== null ? trim($field) : $field;
                    $value = $value !== null ? trim($value) : $value;

                    foreach ($row as $rowField => $rowValue) {
                        switch ($comparison) {
                            case '<=':
                                if (($rowField == $field || $field == '*') && $rowValue > $value) {
                                    return false;
                                }
                                break;
                            case '>=':
                                if (($rowField == $field || $field == '*') && $rowValue < $value) {
                                    return false;
                                }
                                break;
                            case '<>':
                            case '!=':
                                if (($rowField == $field || $field == '*') && $rowValue == $value) {
                                    return false;
                                }
                                break;
                            case '=':
                                if (($rowField == $field || $field == '*') && $rowValue != $value) {
                                    return false;
                                }
                                break;
                            case '<':
                                if (($rowField == $field || $field == '*') && $rowValue >= $value) {
                                    return false;
                                }
                                break;
                            case '>':
                                if (($rowField == $field || $field == '*') && $rowValue <= $value) {
                                    return false;
                                }
                                break;
                            default:
                                Core_Logger::getInstance(__CLASS__)->exception(['Unknown comparison operator {$0}', $comparison], __FILE__, __LINE__);
                        };
                    }
                }

                return true;
            };
        };

        return array_filter($rows, $filterFunction((array)$filterScheme));
    }

    /**
     * Group array by known column
     *
     * @param  $array
     * @param  $columns
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   0.0
     */
    public static function group($array, $columns)
    {
        $group = [];

        $keys = array_flip((array)$columns);

        $indexes = [];

        $i = 0;

        foreach ($array as $key => $item) {
            $values = array_intersect_key($item, $keys);

            $index = implode('__', $values);

            if (!isset($indexes[$index])) {
                $indexes[$index] = $i++;
                $group[$indexes[$index]] = $values;
                $group[$indexes[$index]]['items'] = [];
            }

            $group[$indexes[$index]]['items'][$key] = array_diff_key($item, $keys);
        }

        return $group;
    }

    /**
     * This file is part of the array_column library
     *
     * For the full copyright and license information, please view the LICENSE
     * file that was distributed with this source code.
     *
     * @copyright Copyright (c) 2013 Ben Ramsey <http://benramsey.com>
     * @license   http://opensource.org/licenses/MIT MIT
     *
     *
     * Returns the values from a single column of the input array, identified by
     * the $columnKey.
     *
     * Optionally, you may provide an $indexKey to index the values in the returned
     * array by the values from the $indexKey column in the input array.
     *
     * @param array $input A multi-dimensional array (record set) from which to pull
     * a column of values.
     * @param mixed $columnKey The column(s) of values to return. This value may be
     * null, 0, array or any string
     * may be the string key name for an associative array.
     * @param mixed $indexKey (Optional.) The column to use as the index/keys for
     * the returned array. This value may be null, '', or any string
     * of the column, or it may be the string key name.
     * @return array
     */
    public static function column($input, $columnKey = null, $indexKey = null)
    {
        if (!is_array($input)) {
            Core_Logger::getInstance(__CLASS__)->exception(
                'array_column() expects parameter 1 to be array, ' . gettype($input) . ' given',
                __FILE__,
                __LINE__
            );
        }

        if (!is_int($columnKey) &&
            !is_float($columnKey) &&
            !is_string($columnKey) &&
            !is_array($columnKey) &&
            $columnKey !== null &&
            !(is_object($columnKey) && method_exists($columnKey, '__toString'))
        ) {
            Core_Logger::getInstance(__CLASS__)->exception(
                'array_column(): The column key should be either a string or an integer or array',
                __FILE__,
                __LINE__
            );
        }

        if (isset($indexKey) &&
            !is_int($indexKey) &&
            !is_float($indexKey) &&
            !is_string($indexKey) &&
            !is_array($indexKey) &&
            !(is_object($indexKey) && method_exists($indexKey, '__toString'))
        ) {
            Core_Logger::getInstance(__CLASS__)->exception(
                'array_column(): The index key should be either a string or an integer or array',
                __FILE__,
                __LINE__
            );
        }

        if (isset($indexKey)) {
            if (is_float($indexKey) || is_int($indexKey)) {
                $indexKey = (int)$indexKey;
            } elseif (is_array($indexKey)) {
                if (empty($indexKey)) {
                    $indexKey = null;
                }
            } else {
                $indexKey = (string)$indexKey;
            }
        }

        $resultArray = array();

        foreach ($input as $key => $row) {
            $value = null;
            $valueSet = false;

            if ($indexKey !== null && is_array($indexKey)) {
                $key = implode('__', array_intersect_key($row, array_flip($indexKey)));
            } elseif ($indexKey !== null && array_key_exists($indexKey, $row)) {
                $key = (string)$row[$indexKey];
            } elseif ($indexKey === '') {
                $key = '';
            }

            if ($columnKey === null) {
                $valueSet = true;
                $value = $row;
            } elseif ($columnKey === 0) {
                $valueSet = true;
                $value = reset($row);
            } else {
                if (is_array($columnKey)) {
                    $valueSet = true;
                    $value = implode('__', array_intersect_key($row, array_flip($columnKey)));
                } else {
                    if (array_key_exists($columnKey, $row)) {
                        $valueSet = true;
                        $value = $row[$columnKey];
                    }
                }
            }

            if ($valueSet) {
                if ($key === '') {
                    $resultArray[] = $value;
                } else {
                    $resultArray[$key] = $value;
                }
            }
        }

        return $resultArray;
    }

    /**
     * Ice array diff
     *
     * Return array of added, deleted and other rows
     *
     * @param  $old
     * @param  $new
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public static function diff($old, $new)
    {
        $diff = [
            'added' => array_diff_key($new, $old),
            'deleted' => array_diff_key($old, $new)
        ];

        $diff['other'] = array_diff_key($new, $diff['added']);

        return $diff;
    }

    /**
     * Apply default values to array
     *
     * @param  array $data
     * @param  array $defaults
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.3
     */
    public static function defaults(array $defaults, array $data = null)
    {
        $data = (array)$data;

        foreach ($defaults as $key => $value) {
            if (!array_key_exists($key, $data)) {
                $data[$key] = is_callable($value) ? $value($key) : $value;
            }
        }

        return $data;
    }

    /**
     * Validate array
     *
     * @param  array $data
     * @param  array $validators
     * @return bool
     *
     * @author  dp <denis.a.shestakov@gmail.com>
     * @todo    need impements
     * @version 0.3
     * @since   0.3
     */
    public static function validate(array $data, array $validators = array())
    {
        return false;
    }

    /**
     * Convert array data
     *
     * @param  array $data
     * @param  array $converters
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.3
     * @since   0.3
     */
    public static function convert(array $data, array $converters = array())
    {
        foreach ($converters as $key => $value) {
            if (!array_key_exists($key, $data)) {
                $data[$key] = null;
            }

            $data[$key] = $value($data[$key]);
        }

        return $data;
    }

    public static function toJsObjectString(array $array)
    {
        array_walk(
            $array,
            function (&$item, $key) {
                if (is_array($item)) {
                    array_walk(
                        $item,
                        function (&$item) {
                            $item = '\'' . $item . '\'';
                        }
                    );
                    $item = $key . ': [' . implode(', ', $item) . ']';
                } else {
                    $item = $key . ': \'' . $item . '\'';
                }
            }
        );

        return '{' . implode(', ', $array) . '}';
    }

    /**
     * Convert array to string
     *
     * @param  array $array
     * @return string
     */
    public static function toJsArrayString(array $array)
    {
        $string = '';

        foreach ($array as $element) {
            $string .= ',\'' . $element . '\'';
        }

        return '[' . ltrim($string, ',') . ']';
    }

    public static function diffRecursive($aArray1, $aArray2)
    {
        $aReturn = array();

        foreach ($aArray1 as $mKey => $mValue) {
            if (array_key_exists($mKey, $aArray2)) {
                if (is_array($mValue)) {
                    $aRecursiveDiff = Arrays::diffRecursive($mValue, $aArray2[$mKey]);
                    if (count($aRecursiveDiff)) {
                        $aReturn[$mKey] = $aRecursiveDiff;
                    }
                } else {
                    if ($mValue != $aArray2[$mKey]) {
                        $aReturn[$mKey] = $mValue;
                    }
                }
            } else {
                $aReturn[$mKey] = $mValue;
            }
        }
        return $aReturn;
    }

//    public static function isRecursive($array){
//        return strpos(print_r($array, true), '*RECURSION*');
//    }
}
