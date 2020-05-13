<?php

namespace Ice\Validator;

use Ice\Core\Validator;

class Exists extends Validator
{

    /**
     * Validate data
     *
     * example usage:
     * ```php
     *      $params = 'VALIDATOR_PARAM';
     * ```
     * or
     * ```php
     *      $params = [
     *          'PATTERN_NAME' => [
     *              'params' => [
     *                  'PATTERN_PARAM_NAME1' => 'PATTERN_PARAM_VALUE1',
     *                  'PATTERN_PARAM_NAME2 => 'PATTERN_PARAM_VALUE2'
     *              ],
     *              'message => 'validate failed for {$0}',
     *              'exception => 'Ice:Http_Not_Found'
     *              ]
     *          ]
     *      ];
     * ```
     *
     * @param array $data
     * @param $name
     * @param  mixed $params
     * @return bool
     * @author anonymous <email>
     *
     * @version 1.11
     * @since   0.0
     */
    public function validate(array $data, $name, array $params)
    {
        return array_key_exists($name, $data);
    }

    public function getMessage()
    {
        return 'Param \'{$0}\' is not exists';
    }
}