<?php
/**
 * Ice validator implementation numeric class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Validator;

use Ice\Core\Validator;

/**
 * Class Numeric
 *
 * Validate numeric data
 *
 * @see Ice\Core\Validator
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Validator
 *
 * @version stable_0
 * @since stable_0
 */
class Numeric extends Validator
{
    /**
     * Validate data by scheme
     *
     * @example:
     *  'user_name' => [
     *      [
     *          'validator' => 'Ice:Not_Empty',
     *          'message' => 'Введите имя пользователя.'
     *      ],
     *  ],
     *  'name' => 'Ice:Not_Null'
     *
     * @param $data
     * @param null $scheme
     * @return boolean
     */
    public function validate($data, $scheme = null)
    {
        return is_numeric($data);
    }
}