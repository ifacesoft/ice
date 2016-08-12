<?php
/**
 * Ice core validator abstract class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

use Ice\Core;
use Ice\Helper\Validator as Helper_Validator;
use Ice\Widget\Model_Form;

/**
 * Class Validator
 *
 * Abstract validator class
 *
 * @see Ice\Core\Container
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Core
 *
 * @version 0.0
 * @since   0.0
 */
abstract class Validator extends Container
{
    use Stored;

    const DEFAULT_KEY = 'instance';

    /**
     * Return validator instance
     *
     * @param  null $instanceKey
     * @param  null $ttl
     * @param array $params
     * @return Validator
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.1
     * @since   0.1
     */
    public static function getInstance($instanceKey = null, $ttl = null, array $params = [])
    {
        return parent::getInstance($instanceKey, $ttl, $params);
    }

    public static function schemeColumnPlugin($columnName, $table)
    {
        $validators = [];

        switch ($table['columns'][$columnName]['options']['type']) {
            case 'text':
            case 'textarea':
                $validators['Ice:Length_Max'] = (int)$table['columns'][$columnName]['scheme']['length'];
                break;
            default:
        }

        if ($table['columns'][$columnName]['scheme']['nullable'] === false &&
            !in_array($columnName, $table['indexes']['PRIMARY KEY']['PRIMARY'])
        ) {
            $validators[] = 'Ice:Not_Null';
        }

        return $validators ? ['validators' => $validators] : [];
    }

    /**
     * @param $params
     * @param $paramsOptions
     *
     * @author dp <denis.a.shestakov@gmail.com>
     * @todo Заменить на Helper_Array::validate()
     * @version 1.1
     * @since   1.1
     * @return array
     */
    public static function validateParams(array $params, array $paramsOptions)
    {
        foreach ($paramsOptions as $paramName => $options) {
            if (empty($options['validators'])) {
                continue;
            }

            foreach ((array)$options['validators'] as $validatorName => $validatorParams) {
                if (is_int($validatorName)) {
                    $validatorName = $validatorParams;
                    $validatorParams = null;
                }

                $params[$paramName] = Helper_Validator::validate($validatorName, $validatorParams, $params, $paramName);
            }
        }

        return $params;
    }

    /**
     * Default action key
     *
     * @return Core
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.0
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
     * @since   0.4
     */
    protected static function getDefaultClassKey()
    {
        return self::getClass() . '/default';
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
     * @param array $data
     * @param $name
     * @param  mixed $params
     * @return bool
     * @author anonymous <email>
     *
     * @version 1.2
     * @since   0.0
     */
    abstract public function validate(array $data, $name, array $params);

    public function getMessage()
    {
        return 'param \'{$0}\' with value \'{$1}\' is not valid';
    }
}
