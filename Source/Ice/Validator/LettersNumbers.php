<?php
namespace Ice\Validator;

use Ice\Core\Validator;

/**
 * Class LettersNumbers
 *
 * @see     Ice\Core\Validator
 * @package Ice\Validator;
 * @author  dp <email>
 */
class LettersNumbers extends Validator
{
    /**
     * Check for alphanumeric character(s)
     *
     * example usage:
     * ```php
     *      $scheme = [
     *          'login' => 'LettersNumbers',
     *          // ...
     *      ];
     * ```
     * or
     * ```php
     *      $scheme = [
     *          'name' => [
     *              'Ice:LettersNumbers' => [
     *                  'message => 'not matched'
     *              ]
     *          ],
     *          // ...
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
        return ctype_alnum($data);
    }
}
