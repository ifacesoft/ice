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
        $params = is_array($scheme) ? $scheme['params'] : $scheme;

        return preg_match($params, $data);
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
