<?php
/**
 * Ice core validator abstract class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

use Ice;
use Ice\Core;
use Ice\Helper\Object;

/**
 * Class Validator
 *
 * Abstract validator class
 *
 * @see Ice\Core\Container
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Core
 *
 * @version 0.0
 * @since 0.0
 */
abstract class Validator extends Factory
{
    use Core;

    /**
     * Validate data by validate scheme
     *
     * example usage:
     * ```php
     *      $validateScheme = [
     *          'FIELD_NAME' => 'VALIDATOR_NAME'
     *      ];
     * ```
     * or
     * ```php
     *      $validateScheme = [
     *          FIELD_NAME' => [
     *              'VALIDATOR_NAME' => 'VALIDATOR_PARAM'
     *          ]
     *      ];
     * ```
     * or
     * ```php
     *      $validateScheme = [
     *          'FIELD_NAME' => [
     *              'VALIDATOR_NAME' => [
     *                  'params' => [
     *                      'VALIDATOR_PARAM_NAME1' => 'VALIDATOR_PARAM_VALUE1',
     *                      'VALIDATOR_PARAM_NAME2 => 'VALIDATOR_PARAM_VALUE2'
     *                  ],
     *                  'message => 'validate failed for {$0}'
     *              ]
     *          ]
     *      ];
     * ```
     *
     * @param $data
     * @param array $validateScheme
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @todo Заменить на Helper_Array::validate()
     *
     * @version 0.0
     * @since 0.0
     */
    public static function validateByScheme(array $data, array $validateScheme)
    {
        $errors = '';

        foreach ($validateScheme as $param => $validators) {
            foreach ((array)$validators as $validatorName => $params) {
                $validator = null;

                if (is_int($validatorName)) {
                    $validatorName = $params;
                    $params = null;
                }

                $validator = Validator::getInstance($validatorName);

                if ($validator->validate($data[$param], $params)) {
                    continue;
                }

                $validatorClassName = $validator::getClassName();

                $errors .= !empty($params) && isset($params['message'])
                    ? Validator::getLogger()->info([$validatorClassName . ': ' . $params['message'], $param], Logger::WARNING)
                    : Validator::getLogger()->info([$validatorClassName . ': param {$0} is not valid', $param], Logger::WARNING);
            }
        }

        return $errors;
    }

    /**
     * Create new instance of validator
     *
     * @param $class
     * @param null $hash
     * @return Validator
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    protected static function create($class, $hash = null)
    {
        $class = Object::getClass(__CLASS__, $class);
        return new $class();
    }

    /**
     * Validate data
     *
     * example usage:
     * ```php
     *      $params = 'VALIDATOR_PARAM';
     * ```
     * or
     * ```php
     *      $params = [
     *          'PATTERN_NAME' => [
     *              'params' => [
     *                  'PATTERN_PARAM_NAME1' => 'PATTERN_PARAM_VALUE1',
     *                  'PATTERN_PARAM_NAME2 => 'PATTERN_PARAM_VALUE2'
     *              ],
     *              'message => 'validate failed for {$0}'
     *              ]
     *          ]
     *      ];
     * ```
     *
     * @param $value
     * @param mixed $params
     * @return boolean
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since 0
     */
    public abstract function validate($value, $params = null);

    /**
     * Return validator instance
     *
     * @param null $key
     * @param null $ttl
     * @return Validator
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.1
     * @since 0.1
     */
    public static function getInstance($key = null, $ttl = null)
    {
        return parent::getInstance($key, $ttl);
    }
}