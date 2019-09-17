<?php
/**
 * Ice helper json class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Helper;

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
     * @return array
     * @throws \Exception
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public static function decode($json)
    {
        $data = json_decode($json, true);

        $error = json_last_error();

        if (!$error) {
            return $data;
        }

        switch ($error) {
            case JSON_ERROR_DEPTH:
                throw new \Exception('JSON - Достигнута максимальная глубина стека', print_r($json, true));
            case JSON_ERROR_STATE_MISMATCH:
                throw new \Exception('JSON - Некорректные разряды или не совпадение режимов', print_r($json, true));
            case JSON_ERROR_CTRL_CHAR:
                throw new \Exception('JSON - Некорректный управляющий символ', print_r($json, true));
            case JSON_ERROR_SYNTAX:
                throw new \Exception(
                    'JSON - Синтаксическая ошибка, не корректный JSON',
                    print_r(
                        $json,
                        true
                    )
                );
            case JSON_ERROR_UTF8:
                throw new \Exception('JSON - Некорректные символы UTF-8, возможно неверная кодировка', print_r($json, true));
            default:
                throw new \Exception('JSON - Неизвестная ошибка', print_r($json, true));
        }
    }

    /**
     * Encode data to json string
     *
     * @param  mixed $data
     * @param  int $options
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.0
     */
    public static function encode($data, $options = JSON_UNESCAPED_UNICODE)
    {
        return json_encode($data, $options);
    }
}
