<?php

namespace ice\core\data\validator;

use ice\core\Data_Validator;

class Email extends Data_Validator
{
    public function validate($value)
    {
        return $value == filter_var($value, FILTER_VALIDATE_EMAIL);
    }

}