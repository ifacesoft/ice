<?php
namespace Ice\Validator;

use Ice\Core\Validator;
use Ice\Exception\Error;

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
     * @param array $data
     * @param $name
     * @param array $params
     * @return bool
     * @throws Error
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.2
     * @since   0.0
     */
    public function validate(array $data, $name, array $params)
    {
        $value = null;

        if (array_key_exists('name', $params)) {
            $value = $data[$params['name']];
        }

        if ($value === null && array_key_exists('value', $params)) {
            $value = $params['value'];
        }

        if ($value === null && $params) {
            $value = reset($params);
        }

        return $data[$name] === $value;
    }

    public function getMessage()
    {
        return 'Param \'{$0}\' not equal {$2}';
    }
}
