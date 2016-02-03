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
     * @param  mixed|null $scheme
     * @return boolean
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.1
     * @since   0.1
     */
    public function validate($data, $scheme = null)
    {
        return ctype_alnum($data);
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
