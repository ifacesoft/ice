<?php

namespace ice\core\data\validator;

use ice\core\Data_Validator;

class Emptys extends Data_Validator
{
    public function validate($value)
    {
        return (bool)empty ($value);
    }
}