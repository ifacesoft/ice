<?php

namespace Ice\Validator;

use Ice\Core\Validator;

/**
 * Class Length_Max
 *
 * Validate length max data
 *
 * @see Ice\Core\Validator
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Validator
 */
class Length_Equal extends Not_Null
{
    /**
     * Validate data by scheme
     *
     * @example:
     *  'user_name' => [
     *      [
     *          'validator' => ['Ice:Length_Max' => 255],
     *          'message' => 'Введите имя пользователя.'
     *      ],
     *  ],
     *  'name' => 'Ice:Not_Null'
     *
     * @param array $data
     * @param $name
     * @param  array $params
     * @return bool
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.2
     * @since   0.0
     */
    public function validate(array $data, $name, array $params)
    {
        $length = null;

        if (array_key_exists('length', $params)) {
            $length = $params['length'];
        }

        if ($length === null && $params) {
            $length = reset($params);
        }

        return parent::validate($data, $name, $params) && $length !== null && mb_strlen($data[$name]) === (int)$length;
    }
}
