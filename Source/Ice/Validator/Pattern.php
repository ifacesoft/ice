<?php
/**
 * Ice validator implementation pattern class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Validator;

use Ice\Core\Validator;

/**
 * Class Pattern
 *
 * Validate pattern data
 *
 * @see Ice\Core\Validator
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Validator
 */
class Pattern extends Validator
{
    const LETTERS_ONLY = '/^[a-z]+$/i';

    /**
     * Validate data by pattern
     *
     * example usage:
     * ```php
     *      $scheme = [
     *          'name' => [
     *              'Ice:Pattern' => '/^[a-z]+$/i'
     *          ]
     *      ];
     * ```
     * or
     * ```php
     *      $scheme = [
     *          'name' => [
     *              'Ice:Pattern' => [
     *                  'params' => '/^[a-z]+$/i'
     *                  'message => 'not matched'
     *              ]
     *          ]
     *      ];
     * ```
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
        $pattern = null;

        if (array_key_exists('pattern', $params)) {
            $pattern = $params['pattern'];
        }

        if ($pattern === null && $params) {
            $pattern = reset($params);
        }

        return preg_match($pattern, $data[$name]);
    }

    public function getMessage()
    {
        return 'Param \'{$0}\' with value \'{$1}\' not match pattern \'{$2}\'';
    }
}
