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
 * Проверка в базе существования логина
 */
class Login_Exists extends Data_Validator
{
    public function validateEx($field, $value, $params)
    {
        return !Account::getQuery()
            ->eq('login', $value)
            ->limit(1)
            ->execute()
            ->asValue();
    }
}