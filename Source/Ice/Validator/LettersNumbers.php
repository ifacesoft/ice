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
     * @param array $data
     * @param $name
     * @param  array $params
     * @return bool
     * @internal param $data
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.2
     * @since   0.0
     */
    public function validate(array $data, $name, array $params)
    {
        return ctype_alnum($data[$name]);
    }
}
