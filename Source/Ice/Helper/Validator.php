<?php namespace Ice\Helper;

use Ice\Core\Debuger;
use Ice\Core\Exception;
use Ice\Core\Logger as Core_Logger;
use Ice\Core\Validator as Core_Validator;

class Validator
{
    /**
     * @param $validatorClass
     * @param $validatorParams
     * @param $param
     * @param $value
     * @return mixed
     * @throws Exception
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   0.0
     */
    public static function validate($validatorClass, $validatorParams, $value, $param = null)
    {
        /**
         * @var Core_Validator $validatorClass
         */
        $validatorClass = Core_Validator::getClass($validatorClass);

        /** @var Core_Validator $validator */
        $validator = $validatorClass::getInstance();

        if ($validatorParams && isset($validatorParams['message'])) {
            $message = $validatorParams['message'];
            unset($validatorParams['message']);
        } else {
            $message = $validator->getMessage();
        }

        if ($validatorParams && isset($validatorParams['exception'])) {
            $exceptionClass = Exception::getClass($validatorParams['exception']);
            unset($validatorParams['exception']);
        } else {
            $exceptionClass = Exception::getClass('Ice:Not_Valid');
        }

        if ($validator->validate($value, $validatorParams)) {
            return $value;
        }

        $exceptionMessage = [$message, array_merge([$param, print_r($value, true)], (array)$validatorParams)];

        return Core_Logger::getInstance(__CLASS__)->exception($exceptionMessage, __FILE__, __LINE__, null, null, -1, $exceptionClass);
    }
}
