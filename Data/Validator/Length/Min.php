<?php
/**
 * Created by JetBrains PhpStorm.
 * User: denis
 * Date: 17.04.13
 * Time: 21:19
 * To change this template use File | Settings | File Templates.
 */

namespace ice\core\data\validator;

use ice\core\Data_Validator;

class Length_Min extends Data_Validator
{
    public function validateEx($field, $value, $params)
    {
        return strlen($value) >= $params['minLength'];
    }

}