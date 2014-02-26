<?php

namespace ice\core\data\validator;

use ice\core\Data_Validator;

class Phone extends Data_Validator
{
    public function validate($value)
    {
        if (Helper_Phone::parseMobile($value)) {
            return true;
        }
        return false;
    }
}