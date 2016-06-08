<?php
/**
 * Ice validator implementation length max class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Validator;

use Ice\Core\Debuger;
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
class Length_Max extends Validator
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
    public function validate($data, $scheme = null)
    {
        $scheme = (array) $scheme;
        
        return strlen($data) <= (int)reset($scheme);
    }

    public function getMessage()
    {
        return 'Length of param \'{$0}\' mast be less then {$2} characters';
    }
}
