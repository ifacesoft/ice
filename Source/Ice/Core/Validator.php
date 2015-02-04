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
abstract class Validator extends Container
{
    use Core;

    const DEFAULT_KEY = 'instance';

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
     *                  'message' => 'validate failed for {$0}',
     *                  'exception' => 'Ice:Http_Not_Found'
     *              ]
     *          ]
     *      ];
     * ```
     *
     * @param $data
     * @param array $validateScheme
     * @return array
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
        foreach ($validateScheme as $param => $validators) {
            foreach ((array)$validators as $validatorName => $params) {
                if (is_int($validatorName)) {
                    $validatorName = $params;
                    $params = null;
                }

                $exceptionClass = null;

                /** @var Validator $validatorClass */
                $validatorClass = Validator::getClass($validatorName);

                $value = isset($data[$param])
                    ? $data[$param]
                    : null;

                if ($validatorClass::getInstance()->validate($value, $params)) {
                    continue;
                }

                $validator = 'Validator:' . $validatorClass::getClassName() . ' -> ';

                $message = empty($params) || !isset($params['message'])
                    ? [$validator . 'param \'{$0}\' with value \'{$1}\' is not valid', [$param, print_r($value, true)]]
                    : [$validator . $params['message'], [$param, print_r($value, true)]];

                if (!$exceptionClass) {
                    $exceptionClass = empty($params) || !isset($params['exception'])
                        ? Exception::getClass('Ice:Not_Valid')
                        : Exception::getClass($params['exception']);
                }

                throw new $exceptionClass($message);
            }
        }

        return $data;
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
     *              'message => 'validate failed for {$0}',
     *              'exception => 'Ice:Http_Not_Found'
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

    /**
     * Create new instance of validator
     *
     * @param $key
     * @return Validator
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    protected static function create($key)
    {
        $class = self::getClass();
        return new $class();
    }

    /**
     * Default action key
     *
     * @return Core
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.0
     */
    protected static function getDefaultKey()
    {
        return Validator::DEFAULT_KEY;
    }

    /**
     * Default class key
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    protected static function getDefaultClassKey()
    {
        return self::getClass() . '/default';
    }
}