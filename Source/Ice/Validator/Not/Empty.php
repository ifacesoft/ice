<?php
/**
 * Ice validator implementation not empty class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Validator;

use Ice\Core\Validator;

/**
 * Class Not_Empty
 *
 * Validate not empty data
 *
 * @see Ice\Core\Validator
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Validator
 */
class Not_Empty extends Validator
{
    /**
     * Validate data for not empty
     *
     * example usage:
     * ```php
     *      $scheme = [
     *          'name' => 'Ice:Not_Empty'
     *      ];
     * ```
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
        return !empty($data[$name]);
    }
}
