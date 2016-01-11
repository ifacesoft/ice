<?php namespace Ice\Helper;

use Ice\Core\Exception;
use Ice\Core\Logger as Core_Logger;
use Ice\Core\Validator as Core_Validator;

class Validator
{
    public static function validate($validatorClass, $validatorParams, $param, $value)
    {
        /**
         * @var Core_Validator $validatorClass
         */
        $validatorClass = Core_Validator::getClass($validatorClass);

        if ($validatorClass::getInstance()->validate($value, $validatorParams)) {
            return;
        }

        $validator = 'Validator:' . $validatorClass::getClassName() . ' -> ';

        $message = empty($validatorParams) || !isset($validatorParams['message'])
            ? [$validator . 'param \'{$0}\' with value \'{$1}\' is not valid', [$param, print_r($value, true)]]
            : [$validator . $validatorParams['message'], [$param, print_r($value, true)]];

        $exceptionClass = empty($validatorParams) || !isset($validatorParams['exception'])
            ? Exception::getClass('Ice:Not_Valid')
            : Exception::getClass($validatorParams['exception']);

        Core_Logger::getInstance(__CLASS__)->exception($message, __FILE__, __LINE__, null, null, -1, $exceptionClass);
    }
}
