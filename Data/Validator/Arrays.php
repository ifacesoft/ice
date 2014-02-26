<?php

namespace ice\core\data\validator;

use ice\core\Data_Validator;

class Arrays extends Data_Validator
{

    public function validate($value)
    {
        return (bool)is_array($value);
    }

}