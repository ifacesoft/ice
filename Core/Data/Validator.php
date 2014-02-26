<?php

namespace ice\core;

/**
 *
 * @desc Абстрактный класс валидатора
 * @author Юрий
 * @package Ice
 *
 */
abstract class Data_Validator
{

    const INVALID = 'invalid';

    /**
     * @desc Валидация строки
     * @param string $value Данные.
     * @return true|string
     *         true, если данные прошли валидацию или
     *         строка ошибки.
     */
    public function validate($value)
    {
        return true;
    }

    /**
     * @desc Валидация поля с использованием схемы
     * @param string $field
     *         Название поля.
     * @param mixed $value
     *         Значение поля.
     * @param array $params
     *         Все данные.
     * @return bool
     */
    public function validateEx($field, $value, $params)
    {
        return $this->validate($value);
    }

}