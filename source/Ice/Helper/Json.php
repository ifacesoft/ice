<?php
/**
 * Ice helper json class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Helper;

use Ice\Core\Debuger;
use Ice\Exception\Error;

/**
 * Class Json
 *
 * Helper for json
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Helper
 */
class Json
{
    /**
     * Decode json string to data
     *
     * @param  $json
     * @param bool $requireValid
     * @param bool $assoc
     * @return array
     * @throws Error
     * @throws \Ice\Exception\FileNotFound
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.10
     * @since   0.0
     */
    public static function decode($json, $requireValid = true, $assoc = true)
    {
        if (empty($json)) {
            return [];
        }

        $data = json_decode($json, $assoc);

        $error = json_last_error();

        if (!$error) {
            return $data;
        }

        if (!$requireValid) {
            return [];
        }

        switch ($error) {
            case JSON_ERROR_DEPTH:
                throw new Error('JSON - Достигнута максимальная глубина стека', print_r($json, true));
            case JSON_ERROR_STATE_MISMATCH:
                throw new Error('JSON - Некорректные разряды или не совпадение режимов', print_r($json, true));
            case JSON_ERROR_CTRL_CHAR:
                throw new Error('JSON - Некорректный управляющий символ', print_r($json, true));
            case JSON_ERROR_SYNTAX:
                throw new Error('JSON - Синтаксическая ошибка, не корректный JSON', print_r($json, true));
            case JSON_ERROR_UTF8:
                throw new Error('JSON - Некорректные символы UTF-8, возможно неверная кодировка', print_r($json, true));
            default:
                throw new Error('JSON - Неизвестная ошибка', print_r($json, true));
        }
    }

    /**
     * Encode data to json string
     *
     * @param  mixed $data
     * @param int $options
     * @return string
     * @throws \Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.10
     * @since   0.0
     */
    public static function encode($data, $options = JSON_UNESCAPED_UNICODE)
    {
        if (empty($data)) {
            return '[]';
        }
        
        $json = json_encode($data, $options);

        if ($error = json_last_error()) {
            throw new Error(['JSON Error #' . $error . ': {$0}', json_last_error_msg()], print_r($data, true));
        }

//        return preg_replace('/(?<!\\\)\'/', '\\\'', $json);

        return $json;
    }

    public static function schemeColumnScheme($columnName, $table, $tablePrefixes)
    {
        if (!Type_String::endsWith($columnName, '__json')) {
            return [];
        }

        return [
            'nullable' => false,
            'default' => '[]',
        ];
    }
}
