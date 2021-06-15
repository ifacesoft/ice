<?php
/**
 * Ice core form abstract class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

use Ice\Core;
use Ice\Helper\Arrays;
use Ice\Helper\Emmet;
use Ice\Helper\Json;
use Ice\Helper\Type_String;
use Ice\Render\Php;
use Ice\Widget\Form\Model as Widget_Form_Model;

/**
 * class Widget_Form
 *
 * Core Widget_Form class
 *
 * @see Ice\Core\Container
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Core
 */
abstract class Widget_Form extends Widget
{
    const SUBMIT_EVENT_ONCHANGE = 'onchange';
    const SUBMIT_EVENT_ONSUBMIT = 'onsubmit';

    const FIELD_HIDDEN = 'Hidden';
    const FIELD_TEXT = 'Text';
    const FIELD_DATE = 'Date';
    const FIELD_CHECKBOX = 'Checkbox';
    const FIELD_GEO = 'Geo';
    const FIELD_NUMBER = 'Number';
    const FIELD_PASSWORD = 'Password';
    const FIELD_TEXTAREA = 'Textarea';
    const NAME_MODEL = 'Model';
    const NAME_SIMPLE = 'Simple';

    /**
     * Fields - form parts
     *
     * @var array
     */
    protected $fields = [];
    /**
     * Not ignored fields
     *
     * @var array
     */
    protected $filterFields = [];
    /**
     * Validate scheme for validate fields
     *
     * @var array
     */
    protected $validateScheme = [];

    /**
     * Default field options
     */
    private $defaultOptions = [
        'placeholder' => 'Enter value...',
        'disabled' => false,
        'readonly' => false
    ];

    private $token = null;

    public static function schemeColumnPlugin($columnName, $table)
    {
        return isset(Widget_Form_Model::$typeMap[$table['columns'][$columnName]['scheme']['dataType']])
            ? Widget_Form_Model::$typeMap[$table['columns'][$columnName]['scheme']['dataType']]
            : 'text';
    }

    protected static function getDefaultClassKey()
    {
        return 'Ice:Simple';
    }

    protected static function getDefaultKey()
    {
        return 'default';
    }

    public static function create($url, $action, $block = null, $event = null)
    {
        $widget = parent::create($url, $action, $block, $event);

        $widget->token = md5(Type_String::getRandomString());

        $widget->hidden('token', ['default' => $widget->getToken()]);

        return $widget;
    }


    /**
     * Add hidden type field
     *
     * @param  $fieldName
     * @param  array $options
     * @param  string $template
     * @return Widget_Form
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.0
     */
    public function hidden($fieldName, array $options = [], $template = 'Hidden')
    {
        return $this->addField($fieldName, 'hidden', $options, $template);
    }

    /**
     * Add field as form part
     *
     * @param  $fieldName
     * @param  $fieldTitle
     * @param  array $options
     * @param  $template
     * @return Widget_Form
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.0
     */
    private function addField($fieldName, $fieldTitle, array $options, $template)
    {
        if (!empty($options['validators'])) {
            $this->validateScheme[$fieldName] = $options['validators'];
            unset($options['validators']);
        }

        $this->fields[$fieldName] = [
            'title' => $fieldTitle,
            'options' => Arrays::defaults($this->defaultOptions, $options),
            'template' => $template
        ];

        if (isset($this->fields[$fieldName]['options']['default'])) {
            $this->addValue($fieldName, $this->fields[$fieldName]['options']['default']);
        }

        return $this;
    }

    /**
     * Add password type field
     *
     * @param  $fieldName
     * @param  $fieldTitle
     * @param  array $options
     * @param  string $template
     * @return Widget_Form
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.1
     */
    public function password($fieldName, $fieldTitle, array $options = [], $template = 'Password')
    {
        return $this->addField($fieldName, $fieldTitle, $options, $template);
    }

    /**
     * Add number type field
     *
     * @param  $fieldName
     * @param  $fieldTitle
     * @param  array $options
     * @param  string $template
     * @return Widget_Form
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.0
     */
    public function number($fieldName, $fieldTitle, array $options = [], $template = 'Number')
    {
        return $this->addField($fieldName, $fieldTitle, $options, $template);
    }

    /**
     * Add text type field
     *
     * @param  $fieldName
     * @param  $fieldTitle
     * @param  array $options
     * @param  string $template
     * @return Widget_Form
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.0
     */
    public function text($fieldName, $fieldTitle, array $options = [], $template = 'Text')
    {
        return $this->addField($fieldName, $fieldTitle, $options, $template);
    }

    /**
     * Add date type field
     *
     * @param  $fieldName
     * @param  $fieldTitle
     * @param  array $options
     * @param  string $template
     * @return Widget_Form
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.0
     */
    public function date($fieldName, $fieldTitle, array $options = [], $template = 'Date')
    {
        return $this->addField($fieldName, $fieldTitle, $options, $template);
    }

    /**
     * Add checkbox type field
     *
     * @param  $fieldName
     * @param  $fieldTitle
     * @param  array $options
     * @param  string $template
     * @return Widget_Form
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.0
     */
    public function checkbox($fieldName, $fieldTitle, array $options = [], $template = 'Checkbox')
    {
        return $this->addField($fieldName, $fieldTitle, $options, $template);
    }

    /**
     * Add combobox type field
     *
     * @param  $fieldName
     * @param  $fieldTitle
     * @param  array $options
     * @param  string $template
     * @return Widget_Form
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.0
     */
    public function combobox($fieldName, $fieldTitle, array $options = [], $template = 'Combobox')
    {
        return $this->addField($fieldName, $fieldTitle, $options, $template);
    }

    /**
     * Add map type field
     *
     * @param  $fieldName
     * @param  $fieldTitle
     * @param  array $options
     * @param  string $template
     * @return Widget_Form
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.0
     */
    public function geo($fieldName, $fieldTitle, array $options = [], $template = 'Geo')
    {
        return $this->addField($fieldName, $fieldTitle, $options, $template);
    }

    /**
     * Add textarea type field
     *
     * @param  $fieldName
     * @param  $fieldTitle
     * @param  array $options
     * @param  string $template
     * @return Widget_Form
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.0
     */
    public function textarea($fieldName, $fieldTitle, array $options = [], $template = 'Textarea')
    {
        return $this->addField($fieldName, $fieldTitle, $options, $template);
    }

    public function file($fieldName, $fieldTitle, array $options = [], $template = 'File')
    {
        return $this->addField($fieldName, $fieldTitle, $options, $template);
    }

    public function button($fieldName, $fieldTitle, array $options = [], $template = 'Button')
    {
        return $this->addField($fieldName, $fieldTitle, $options, $template);
    }

    /**
     * Validate form by validate scheme
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
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
     * @since   0.0
     */
    public function getFilterFields()
    {
        return $this->filterFields;
    }

    /**
     * Return validate scheme
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function getValidateScheme()
    {
        return $this->validateScheme;
    }

    /**
     * @param null $name
     * @return null|string
     */
    public function getValues($name = null)
    {
        $var = $this->getFilterFields();

        return empty($var)
            ? parent::getValues($name)
            : array_intersect_key(parent::getValues($name), array_flip($var));
    }

    /**
     * Add accepted fields
     *
     * @param  array $filterFields
     * @return Widget_Form
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function addFilterFields(array $filterFields)
    {
        if (empty($filterFields)) {
            return $this;
        }

        $this->filterFields = array_merge($this->filterFields, $filterFields);
        return $this;
    }

    /**
     * Submit form
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    abstract public function submit();

    public function setInput($params)
    {
        foreach ($this->getFields() as $fieldName => $field) {
            if ($param = strstr($params[$fieldName], '/' . Query_Builder::SQL_ORDERING_ASC, false) !== false) {
                $this->addValue($fieldName, $param);
                continue;
            }

            if ($param = strstr($params[$fieldName], '/' . Query_Builder::SQL_ORDERING_DESC, false) !== false) {
                $this->addValue($fieldName, $param);
                continue;
            }

            $this->addValue($fieldName, isset($params[$fieldName]) ? $params[$fieldName] : null);
        }

        return $this;
    }

    /**
     * Return fields - form parts
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function getFields()
    {
        return $this->fields;
    }

    public function bind($key, $value)
    {
        if (isset($this->fields[$key])) {
            $this->addValue($key, $value);
        }

        return $value;
    }

    public function render()
    {
        /** @var Widget_Form $formClass */
        $formClass = get_class($this);

        $formName = 'Form_' . $formClass::getClassName();

        $filterFields = $this->getFilterFields();

        $fields = $this->getFields();
        $values = $this->getValues();

        $result = [];

        $targetFields = [];
        foreach ($filterFields as $key => &$value) {
            if (is_string($key)) {
                if (is_array($value)) {
                    list($fields[$key]['type'], $fields[$key]['template']) = $value;
                } else {
                    $fields[$key]['type'] = $value;
                    $fields[$key]['template'] = 'Ice:' . $value;
                }

                $value = $key;
            }

            $targetFields[$value] = $fields[$value];
            unset($fields[$value]);
        }

        if (empty($targetFields)) {
            $targetFields = $fields;
        }

        unset($fields);

        foreach ($targetFields as $fieldName => $field) {
            $field['fieldName'] = $fieldName;
            $field['formName'] = $formName;
            $field['value'] = isset($values[$fieldName]) ? $values[$fieldName] : '';

            $field['dataUrl'] = $this->getUrl();
            $field['dataJson'] = Json::encode($this->getParams());
            $field['dataAction'] = $this->getAction();
            $field['dataBlock'] = $this->getBlock();
            $field['token'] = $this->getToken();

            if ($this->getEvent() == Widget_Form::SUBMIT_EVENT_ONCHANGE && !isset($field['onchange'])) {
                $field['onchange'] = 'Ice_Widget_Form.change($(this)); return false;';
            }

            $templateBaseClass = $field['template'][0] == '_' ? $formClass : Widget_Form::getClass();

            $result[] = Php::getInstance()->fetch($templateBaseClass . '_' . $field['template'], $field);
        }

        $formHtml = Php::getInstance()->fetch(
            $formClass,
            [
                'fields' => $result,
                'formName' => $formName,
                'classes' => $this->getClasses(),
                'style' => $this->getStyle()
            ]
        );

        return $this->getLayout()
            ? Emmet::translate($this->getLayout() . '{{$formHtml}}', ['formHtml' => $formHtml])
            : $formHtml;
    }


    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }
}
