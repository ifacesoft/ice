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
class Length_Equal extends Validator
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
     * @param  $data
     * @param  array $scheme
     * @return boolean
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function validate($data, array $scheme = [])
    {
        return strlen($data) === (int)reset($scheme);
    }

    /**
     * Init object
     *
     * @param array $data
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   1.1
     */
    protected function init(array $data)
    {
        // TODO: Implement init() method.
    }
}
