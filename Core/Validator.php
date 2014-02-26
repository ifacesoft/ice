<?php
/**
 * Created by JetBrains PhpStorm.
 * User: denis
 * Date: 17.04.13
 * Time: 19:36
 * To change this template use File | Settings | File Templates.
 */

namespace ice\core;

use ice\Exception;

class Validator
{

    /**
     * Валидаторы
     * @var array <Data_Validator_Abstract>
     */
    private static $_validators = array();

    /**
     *
     * @param string $name
     * @return Data_Validator
     */
    public static function get($name)
    {
        if (isset(self::$_validators[$name])) {
            return self::$_validators[$name];
        }

        $class = 'Data_Validator_' . $name;
        return self::$_validators[$name] = new $class;
    }

    /**
     * Схема валидации
     * @var array
     */
    protected $validationScheme;
    protected $data = array();

    const DEFAULT_ERROR_MSG = 'Проверьте введенные данные';

    public function __construct($validatorClass)
    {
        $this->validationScheme = Config::get($validatorClass, array(), null, true);
    }

    /**
     * @param $config
     *
     * @return Validator
     */
    public static function create($config)
    {
        return new self($config);
    }

    public function setData(array $data)
    {
        $this->data = array_merge($this->data, $data);
        return $this;
    }

    public function set($key, $value)
    {
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * Получить значение поля валидации
     *
     * @param $fieldName
     *
     * @return null
     */
    public function getFieldValue($fieldName)
    {
        return isset($this->data[$fieldName]) ?
            $this->data[$fieldName] : null;
    }

    /**
     * @param $validatorClass
     *
     * @return Data_Validator
     */
    public function getValidator($validatorClass) // TODO: Стремная реализация.
    {
        return new $validatorClass;
    }

    /**
     * Сгенерить сообщенеи об ошибке
     *
     * @param $validator
     * @param $params
     *
     * @return string
     */
    protected function generateMessage($validator, $params)
    {
        $keys = array_map(
            function ($field) {
                return '{$' . $field . '}';
            },
            array_keys($params)
        );
        $placeholders = array_combine($keys, $params);
        if ($validator['message']) {
            return strtr($validator['message'], $placeholders);
        } else {
            return self::DEFAULT_ERROR_MSG;
        }
    }


    /**
     * Валидация данных по схеме
     *
     * @throws Exception
     * @throws Validator_Exception
     *
     * @return bool
     */
    public function validate()
    {
        $validationScheme = $this->validationScheme;

        if (empty($validationScheme)) {
            throw new Exception('Не установлена схема валидации.');
        }

        foreach ($validationScheme as $field => $validators) {
            $validateValue = $this->getFieldValue($field);
            foreach ($validators as $validator) {
                $validatorObject = $this->getValidator($validator['validator']);
                $validatorParams = isset($validator['params'])
                    ? $validator['params']
                    : array();
                $params = array_merge($validatorParams, $this->data);
                $validationResult = $validatorObject->validateEx($field, $validateValue, $params);
                if (!$validationResult) {
                    throw new Validator_Exception($this->generateMessage($validator, $params));
                }
            }
        }

        return true;
    }
}