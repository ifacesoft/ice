<?php
/**
 * Ice validator implementation length min class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Validator;

use Ice\Core\Debuger;
use Ice\Core\Validator;
use Symfony\Component\Debug\Debug;

/**
 * Class Length_Min
 *
 * Validate length min data
 *
 * @see Ice\Core\Validator
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Validator
 */
class Length_Min extends Not_Null
{
    /**
     * Validate data by scheme
     *
     * @example:
     *  'user_name' => [
     *      [
     *          'validator' => ['Ice:Length_Min' => 2],
     *          'message' => 'Введите имя пользователя.'
     *      ],
     *  ],
     *  'name' => 'Ice:Not_Null'
     *
     * @param array $data
     * @param $name
     * @param array $params
     * @return bool
     *
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

        return parent::validate($data, $name, $params) && $length !== null && mb_strlen($data[$name]) >= (int)$length;
    }

    public function getMessage()
    {
        return 'Length of param \'{$0}\' mast be more then {$2} characters';
    }
}
