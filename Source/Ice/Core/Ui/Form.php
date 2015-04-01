<?php
/**
 * Ice core form abstract class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

use Ice\Form\Model as Ui_Form_Model;
use Ice\Core;
use Ice\Helper\Arrays;

/**
 * Class Ui_Form
 *
 * Core Ui_Form class
 *
 * @see Ice\Core\Container
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Core
 */
abstract class Ui_Form extends Container
{
    use Ui, Stored;

    /**
     * Fields - form parts
     *
     * @var array
     */
    protected $_fields = [];
    /**
     * Not ignored fields
     *
     * @var array
     */
    protected $_filterFields = [];
    /**
     * Validate scheme for validate fields
     *
     * @var array
     */
    protected $_validateScheme = [];
    /**
     * Binds values
     *
     * @var array
     */
    protected $_values = [];
    /**
     * Default field options
     */
    private $_defaultOptions = [
        'placeholder' => 'Enter value...',
        'disabled' => false,
        'readonly' => false
    ];

    private $_key = null;

    /**
     * Constructor for model forms
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.1
     */
    private function __construct()
    {
    }

    /**
     * Return instance of Ui_Form
     *
     * @param null $key
     * @param null $ttl
     * @return Ui_Form
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
     * Create new instance of form
     *
     * @param $key
     * @return Ui_Form
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.0
     */
    protected static function create($key)
    {
        $class = self::getClass();
//
//        if ($key) {
//            $class .= '_' . $key;
//        }

        $form = new $class();

        $form->_key = $key;

        return $form;
    }

    /**
     * Add hidden type field
     *
     * @param $fieldName
     * @param $fieldTitle
     * @param array $options
     * @param array $validators
     * @param null $value
     * @param string $template
     * @return Ui_Form
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.0
     */
    public function hidden($fieldName, $fieldTitle, array $options = [], array $validators = [], $value = null, $template = 'Field_Hidden')
    {
        return $this->addField($fieldName, $fieldTitle, $options, $validators, $value, $template);
    }

    /**
     * Add field as form part
     *
     * @param $fieldName
     * @param $fieldTitle
     * @param array $options
     * @param array $validators
     * @param $value
     * @param $template
     * @return Ui_Form
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.0
     */
    private function addField($fieldName, $fieldTitle, array $options, array $validators, $value, $template)
    {
        $this->_fields[$fieldName] = [
            'title' => $fieldTitle,
            'options' => Arrays::defaults($this->_defaultOptions, $options),
            'template' => $template
        ];

        $this->_validateScheme[$fieldName] = $validators;
        $this->bind($fieldName, $value);

        return $this;
    }

    /**
     * Bind value
     *
     * @param $key
     * @param null $value
     * @return Ui_Form
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function bind($key, $value = null)
    {
        if (is_array($key)) {
            foreach ($key as $v => $k) {
                $this->bind($v, $k);
            }

            return $this;
        }

        $this->_values[$key] = empty($value) ? null : $value;

        return $this;
    }

    /**
     * Add password type field
     *
     * @param $fieldName
     * @param $fieldTitle
     * @param array $options
     * @param array $validators
     * @param null $value
     * @param string $template
     * @return Ui_Form
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.1
     */
    public function password($fieldName, $fieldTitle, array $options = [], array $validators = [], $value = null, $template = 'Field_Password')
    {
        return $this->addField($fieldName, $fieldTitle, $options, $validators, $value, $template);
    }

    /**
     * Add number type field
     *
     * @param $fieldName
     * @param $fieldTitle
     * @param array $options
     * @param array $validators
     * @param null $value
     * @param string $template
     * @return Ui_Form
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.0
     */
    public function number($fieldName, $fieldTitle, array $options = [], array $validators = [], $value = null, $template = 'Field_Number')
    {
        return $this->addField($fieldName, $fieldTitle, $options, $validators, $value, $template);
    }

    /**
     * Add text type field
     *
     * @param $fieldName
     * @param $fieldTitle
     * @param array $options
     * @param array $validators
     * @param null $value
     * @param string $template
     * @return Ui_Form
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.0
     */
    public function text($fieldName, $fieldTitle, array $options = [], array $validators = [], $value = null, $template = 'Field_Text')
    {
        return $this->addField($fieldName, $fieldTitle, $options, $validators, $value, $template);
    }

    /**
     * Add date type field
     *
     * @param $fieldName
     * @param $fieldTitle
     * @param array $options
     * @param array $validators
     * @param null $value
     * @param string $template
     * @return Ui_Form
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.0
     */
    public function date($fieldName, $fieldTitle, array $options = [], array $validators = [], $value = null, $template = 'Field_Date')
    {
        return $this->addField($fieldName, $fieldTitle, $options, $validators, $value, $template);
    }

    /**
     * Add checkbox type field
     *
     * @param $fieldName
     * @param $fieldTitle
     * @param array $options
     * @param array $validators
     * @param null $value
     * @param string $template
     * @return Ui_Form
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.0
     */
    public function checkbox($fieldName, $fieldTitle, array $options = [], array $validators = [], $value = null, $template = 'Field_Checkbox')
    {
        return $this->addField($fieldName, $fieldTitle, $options, $validators, $value, $template);
    }

    /**
     * Add combobox type field
     *
     * @param $fieldName
     * @param $fieldTitle
     * @param array $options
     * @param array $validators
     * @param null $value
     * @param string $template
     * @return Ui_Form
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.0
     */
    public function combobox($fieldName, $fieldTitle, array $options = [], array $validators = [], $value = null, $template = 'Field_Combobox')
    {
        return $this->addField($fieldName, $fieldTitle, $options, $validators, $value, $template);
    }

    /**
     * Add map type field
     *
     * @param $fieldName
     * @param $fieldTitle
     * @param array $options
     * @param array $validators
     * @param null $value
     * @param string $template
     * @return Ui_Form
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.0
     */
    public function geo($fieldName, $fieldTitle, array $options = [], array $validators = [], $value = null, $template = 'Field_Geo')
    {
        return $this->addField($fieldName, $fieldTitle, $options, $validators, $value, $template);
    }

    /**
     * Add textarea type field
     *
     * @param $fieldName
     * @param $fieldTitle
     * @param array $options
     * @param array $validators
     * @param null $value
     * @param string $template
     * @return Ui_Form
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.0
     */
    public function textarea($fieldName, $fieldTitle, array $options = [], array $validators = [], $value = null, $template = 'Field_Textarea')
    {
        return $this->addField($fieldName, $fieldTitle, $options, $validators, $value, $template);
    }

    /**
     * Return fields - form parts
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getFields()
    {
        return $this->_fields;
    }

    /**
     * Validate form by validate scheme
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function validate()
    {
        $var = $this->getFilterFields();

        $validateScheme = empty($var)
            ? $this->getValidateScheme()
            : array_intersect_key($this->getValidateScheme(), array_flip($var));

        return Validator::validateByScheme($this->getValues(), $validateScheme);
    }

    /**
     * Return not ignored form fields
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getFilterFields()
    {
        return $this->_filterFields;
    }

    /**
     * Return validate scheme
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getValidateScheme()
    {
        return $this->_validateScheme;
    }

    /**
     * Return binded values
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getValues()
    {
        $var = $this->getFilterFields();

        return empty($var)
            ? $this->_values
            : array_intersect_key($this->_values, array_flip($var));
    }

    /**
     * Add accepted fields
     *
     * @param array $filterFields
     * @return Ui_Form
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function addFilterFields(array $filterFields)
    {
        if (empty($filterFields)) {
            return $this;
        }

        $this->_filterFields = array_merge($this->_filterFields, $filterFields);
        return $this;
    }

    /**
     * @return null|string
     */
    public function getKey()
    {
        return $this->_key;
    }

    protected static function getDefaultClassKey()
    {
        return 'Ice:Simple';
    }

    protected static function getDefaultKey()
    {
        return 'default';
    }

    /**
     * Submit form
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since 0
     */
    abstract public function submit();

    public static function schemeColumnPlugin($columnName, $table)
    {
        return isset(Ui_Form_Model::$typeMap[$table['columns'][$columnName]['scheme']['dataType']])
            ? Ui_Form_Model::$typeMap[$table['columns'][$columnName]['scheme']['dataType']]
            : 'text';
    }
}