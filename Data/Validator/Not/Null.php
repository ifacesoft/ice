<?php

namespace ice\core\data\validator;

use ice\core\Data_Validator;

class Data_Validator_Not_Null extends Data_Validator
{
    public function validate($value)
    {
        return $value !== null;
    }

}