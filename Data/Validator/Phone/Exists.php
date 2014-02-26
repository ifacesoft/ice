<?php
/**
 * Created by JetBrains PhpStorm.
 * User: denis
 * Date: 12.05.13
 * Time: 11:22
 * To change this template use File | Settings | File Templates.
 */

namespace ice\core\data\validator;

use ice\core\Data_Validator;

/**
 * Class Data_Validator_Phone_Exists
 * Проверка в базе существования телефона
 */
class Phone_Exists extends Data_Validator
{
    public function validateEx($field, $value, $params)
    {
        //номер телефона без первой цифры
        $phoneNumber = substr(Helper_Phone::parseMobile($value), 1);

        return !Account::getQuery()
            ->where('phone LIKE ?', '%' . $phoneNumber)
            ->limit(1)
            ->execute()
            ->asValue();
    }
}