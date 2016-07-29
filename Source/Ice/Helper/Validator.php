<?php namespace Ice\Helper;

use Ice\Core\Exception;
use Ice\Core\Logger as Core_Logger;
use Ice\Core\Validator as Core_Validator;
use Ice\Exception\Not_Show;
use Ice\Exception\Not_Valid;

class Validator
{
    /**
     * @param $validatorClass
     * @param $validatorOptions
     * @param $name
     * @param $data
     * @return mixed
     * @throws Exception
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.2
     * @since   0.0
     */
    public static function validate($validatorClass, $validatorOptions, array $data, $name)
    {
        /** @var Core_Validator $validatorClass */
        $validatorClass = Core_Validator::getClass($validatorClass);

        /** @var Core_Validator $validator */
        $validator = $validatorClass::getInstance();

        if (is_array($validatorOptions) && !empty($validatorOptions['message'])) {
            $message = $validatorOptions['message'];
            unset($validatorOptions['message']);
        } else {
            $message = $validator->getMessage();
        }

        if (is_array($validatorOptions) && array_key_exists('exception', $validatorOptions)) {
            if ($validatorOptions['exception'] === true) {
                $exceptionClass = Not_Valid::class;
            } else if ($validatorOptions['exception'] === false) {
                $exceptionClass = Not_Show::class;
            } else {
                $exceptionClass = Exception::getClass($validatorOptions['exception']);
            }

            unset($validatorOptions['exception']);
        } else {
            $exceptionClass = Not_Valid::class;
        }

        if (is_array($validatorOptions) && array_key_exists('params', $validatorOptions)) {
            $params = $validatorOptions['params'];
        } else {
            $params = $validatorOptions;
        }

        if ($validator->validate($data, $name, (array)$params)) {
            return $data[$name];
        }

        if ($exceptionClass == Not_Show::class) {
            throw new Not_Show('Content not showing...');
        }

        $exceptionMessage = [$message, array_merge([$name, print_r($data, true)], (array)$validatorOptions)];

        return Core_Logger::getInstance(__CLASS__)->exception($exceptionMessage, __FILE__, __LINE__, null, null, -1, $exceptionClass);
    }
}
