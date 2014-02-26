<?php
/**
 *
 * @desc Помощник для работы с json
 * @package Ice
 *
 */

namespace ice\core\helper;

use ice\Exception;

class Json
{
    /**
     * @param $json
     * @return array
     * @throws Exception
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
                throw new Exception('JSON - Достигнута максимальная глубина стека: ' . "\n" . print_r(
                        $json,
                        true
                    ) . "\n");
            case JSON_ERROR_STATE_MISMATCH:
                throw new Exception('JSON - Некорректные разряды или не совпадение режимов: ' . "\n" . print_r(
                        $json,
                        true
                    ) . "\n");
            case JSON_ERROR_CTRL_CHAR:
                throw new Exception('JSON - Некорректный управляющий символ: ' . "\n" . print_r($json, true) . "\n");
            case JSON_ERROR_SYNTAX:
                throw new Exception('JSON - Синтаксическая ошибка, не корректный JSON: ' . "\n" . print_r(
                        $json,
                        true
                    ) . "\n");
            case JSON_ERROR_UTF8:
                throw new Exception('JSON - Некорректные символы UTF-8, возможно неверная кодировка: ' . "\n" . print_r(
                        $json,
                        true
                    ) . "\n");
            default:
                throw new Exception('JSON - Неизвестная ошибка: ' . "\n" . print_r($json, true) . "\n");
        }
    }

    /**
     * @param $data
     * @param bool $isPretty
     * @return string
     */
    public static function encode($data, $isPretty = false)
    {
        return $isPretty
            ? json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
            : json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}
