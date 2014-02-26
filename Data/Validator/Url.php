<?php

namespace ice\core\data\validator;

use ice\core\Data_Validator;

class Url extends Data_Validator
{

    public function validate($value)
    {
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return true;
        }
        return false;
    }

}