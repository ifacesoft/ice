<?php
/**
 * Ice validator implementation ip class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Validator;

use DateTime;
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
 * @package    Ice
 * @subpackage Validator
 */
class Date extends Validator
{
    /**
     * Validate data by scheme
     *
     * @example:
     *  'user_name' => [
     *      [
     *          'validator' => 'Ice:Ip',
     *          'message' => 'Введите имя пользователя.'
     *      ],
     *  ],
     *  'name' => 'Ice:Not_Null'
     *
     * @param array $data
     * @param $name
     * @param  array $params
     * @return bool
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.2
     * @since   0.0
     */
    public function validate(array $data, $name, array $params)
    {
        $value = array_key_exists($name, $data) ? $data[$name] : null;

        if (!$value) {
            return false;
        }

        if (empty($params)) {
            $format = \Ice\Helper\Date::FORMAT_MYSQL;
        } else {
            $format = isset($params['format']) ? $params['format'] : reset($params);
        }

        $d = DateTime::createFromFormat($format, $value);

        return $d && $d->format($format) == $value;
    }

    public function getMessage()
    {
        return 'Date is wrong format!';
    }
}
