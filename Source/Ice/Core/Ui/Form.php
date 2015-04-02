<?php
/**
 * Ice core form abstract class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

use Ice\Core;
use Ice\Form\Model as Ui_Form_Model;
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
     * Default field options
     */
    private $_defaultOptions = [
        'placeholder' => 'Enter value...',
        'disabled' => false,
        'readonly' => false
    ];

    private $_key = [];

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

        /** @var Ui_Form $form */
        $form = new $class();

        $form->bind($key);

        return $form;
    }

    /**
     * Add hidden type field
     *
     * @param $fieldName
     * @param $fieldTitle
     * @param array $options
     * @param string $template
     * @return Ui_Form
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.0
     */
    public function hidden($fieldName, $fieldTitle, array $options = [], $template = 'Field_Hidden')
    {
        return $this->addField($fieldName, $fieldTitle, $options, $template);
    }

    /**
     * Add field as form part
     *
     * @param $fieldName
     * @param $fieldTitle
     * @param array $options
     * @param $template
     * @return Ui_Form
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.0
     */
    private function addField($fieldName, $fieldTitle, array $options, $template)
    {
        if (!empty($options['validators'])) {
            $this->_validateScheme[$fieldName] = $options['validators'];
            unset($options['validators']);
        }

        if (empty($this->_key[$fieldName]) && array_key_exists('defaultValue', $options)) {
            $this->bind($fieldName, $options['defaultValue']);
        }

        $this->_fields[$fieldName] = [
            'title' => $fieldTitle,
            'options' => Arrays::defaults($this->_defaultOptions, $options),
            'template' => $template
        ];

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
     * @version 0.6
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

        $this->_key[$key] = $value;

        return $this;
    }

    /**
     * Add password type field
     *
     * @param $fieldName
     * @param $fieldTitle
     * @param array $options
     * @param string $template
     * @return Ui_Form
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.1
     */
    public function password($fieldName, $fieldTitle, array $options = [], $template = 'Field_Password')
    {
        return $this->addField($fieldName, $fieldTitle, $options, $template);
    }

    /**
     * Add number type field
     *
     * @param $fieldName
     * @param $fieldTitle
     * @param array $options
     * @param string $template
     * @return Ui_Form
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.0
     */
    public function number($fieldName, $fieldTitle, array $options = [], $template = 'Field_Number')
    {
        return $this->addField($fieldName, $fieldTitle, $options, $template);
    }

    /**
     * Add text type field
     *
     * @param $fieldName
     * @param $fieldTitle
     * @param array $options
     * @param string $template
     * @return Ui_Form
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.0
     */
    public function text($fieldName, $fieldTitle, array $options = [], $template = 'Field_Text')
    {
        return $this->addField($fieldName, $fieldTitle, $options, $template);
    }

    /**
     * Add date type field
     *
     * @param $fieldName
     * @param $fieldTitle
     * @param array $options
     * @param string $template
     * @return Ui_Form
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.0
     */
    public function date($fieldName, $fieldTitle, array $options = [], $template = 'Field_Date')
    {
        return $this->addField($fieldName, $fieldTitle, $options, $template);
    }

    /**
     * Add checkbox type field
     *
     * @param $fieldName
     * @param $fieldTitle
     * @param array $options
     * @param string $template
     * @return Ui_Form
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.0
     */
    public function checkbox($fieldName, $fieldTitle, array $options = [], $template = 'Field_Checkbox')
    {
        return $this->addField($fieldName, $fieldTitle, $options, $template);
    }

    /**
     * Add combobox type field
     *
     * @param $fieldName
     * @param $fieldTitle
     * @param array $options
     * @param string $template
     * @return Ui_Form
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.0
     */
    public function combobox($fieldName, $fieldTitle, array $options = [], $template = 'Field_Combobox')
    {
        return $this->addField($fieldName, $fieldTitle, $options, $template);
    }

    /**
     * Add map type field
     *
     * @param $fieldName
     * @param $fieldTitle
     * @param array $options
     * @param string $template
     * @return Ui_Form
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.0
     */
    public function geo($fieldName, $fieldTitle, array $options = [], $template = 'Field_Geo')
    {
        return $this->addField($fieldName, $fieldTitle, $options, $template);
    }

    /**
     * Add textarea type field
     *
     * @param $fieldName
     * @param $fieldTitle
     * @param array $options
     * @param string $template
     * @return Ui_Form
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.0
     */
    public function textarea($fieldName, $fieldTitle, array $options = [], $template = 'Field_Textarea')
    {
        return $this->addField($fieldName, $fieldTitle, $options, $template);
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

        return Validator::validateByScheme($this->getKey(), $validateScheme);
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
        $var = $this->getFilterFields();

        return empty($var)
            ? $this->_key
            : array_intersect_key($this->_key, array_flip($var));
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