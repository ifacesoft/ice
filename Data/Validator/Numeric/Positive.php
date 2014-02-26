<?php

namespace ice\core\data\validator;

use ice\core\Data_Validator;

class Numeric_Positive extends Data_Validator
{
    public function validate($value)
    {
        return $value > 0;
    }

}