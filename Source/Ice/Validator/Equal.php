<?php
namespace Ice\Validator;

use Ice\Core\Debuger;
use Ice\Core\Validator;

/**
 * Class Equal
 *
 * @see     Ice\Core\Validator
 * @package Ice\Validator;
 * @author  dp <email>
 */
class Equal extends Validator
{
    /**
     * Validate data by pattern
     *
     * example usage:
     * ```php
     *      $scheme = [
     *          'name' => [
     *              'Ice:Equal' => 'Vasya'
     *          ]
     *      ];
     * ```
     * or
     * ```php
     *      $scheme = [
     *          'name' => [
     *              'Ice:Equal' => [
     *                  'params' => 'Vasya'
     *                  'message => 'Not Vasya'
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
        return in_array($data, $scheme);
    }

    public function getMessage()
    {
        return 'Param \'{$0}\' not equal {$2}';
    }
}
