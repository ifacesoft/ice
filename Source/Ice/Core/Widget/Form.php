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
use Ice\Helper\Directory;
use Ice\Helper\Emmet;
use Ice\Helper\Json;
use Ice\View\Render\Php;
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
    const FIELD_HIDDEN = 'Field_Hidden';
    const FIELD_TEXT = 'Field_Text';
    const FIELD_DATE = 'Field_Date';
    const FIELD_CHECKBOX = 'Field_Checkbox';
    const FIELD_RADIOBUTTON = 'Field_Radiobutton';
    const FIELD_NUMBER = 'Field_Number';
    const FIELD_PASSWORD = 'Field_Password';
    const FIELD_TEXTAREA = 'Field_Textarea';
    const FIELD_MAP = 'Field_Map';
    const FIELD_COMBOBOX = 'Field_Combobox';
    const FIELD_FILE = 'Field_File';
    const ELEMENT_BUTTON = 'Element_Button';

    const NAME_MODEL = 'Model';
    const NAME_SIMPLE = 'Simple';


    /**
     * Validate scheme for validate fields
     *
     * @var array
     */
    protected $validateScheme = [];

    /**
     * Default field options
     */
    protected $defaultOptions = [
        'disabled' => false,
        'readonly' => false,
        'required' => false,
        'autofocus' => false
    ];

    protected $onsubmit = null;

    protected static function config() {
        return ['input' => []];
    }

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

    /**
     * Add hidden type field
     *
     * @param $fieldName
     * @param $fieldTitle
     * @param  array $options
     * @param  string $template
     * @return Widget_Form
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.0
     */
    public function hidden($fieldName, $fieldTitle = 'hidden', array $options = [], $template = 'Ice\Core\Widget_Form_Hidden')
    {
        return $this->addPart($fieldName, $fieldTitle, $options, $template, Widget_Form::FIELD_HIDDEN);
    }

    /**
     * Add field as form part
     *
     * @param $partName
     * @param $partTitle
     * @param  array $options
     * @param $template
     * @param $element
     * @return Widget_Form
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.0
     */
    protected function addPart($partName, $partTitle, array $options, $template, $element)
    {
        if (!empty($options['validators'])) {
            $this->validateScheme[$partName] = $options['validators'];
            unset($options['validators']);
        }

        return parent::addPart($partName, $partTitle, $options, $template, $element);
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
    public function password($fieldName, $fieldTitle, array $options = [], $template = 'Ice\Core\Widget_Form_Password')
    {
        return $this->addPart($fieldName, $fieldTitle, $options, $template, Widget_Form::FIELD_PASSWORD);
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
    public function number($fieldName, $fieldTitle, array $options = [], $template = 'Ice\Core\Widget_Form_Number')
    {
        return $this->addPart($fieldName, $fieldTitle, $options, $template, Widget_Form::FIELD_NUMBER);
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
    public function text($fieldName, $fieldTitle, array $options = [], $template = 'Ice\Core\Widget_Form_Text')
    {
        return $this->addPart($fieldName, $fieldTitle, $options, $template, Widget_Form::FIELD_TEXT);
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
    public function date($fieldName, $fieldTitle, array $options = [], $template = 'Ice\Core\Widget_Form_Date')
    {
        return $this->addPart($fieldName, $fieldTitle, $options, $template, Widget_Form::FIELD_DATE);
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
    public function checkbox($fieldName, $fieldTitle, array $options = [], $template = 'Ice\Core\Widget_Form_Checkbox')
    {
        return $this->addPart($fieldName, $fieldTitle, $options, $template, Widget_Form::FIELD_CHECKBOX);
    }

    /**
     * Add radio button type field
     *
     * @param  $fieldName
     * @param  $fieldTitle
     * @param  array $options
     * @param  string $template
     * @return Widget_Form
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.0
     * @since   0.0
     */
    public function radio($fieldName, $fieldTitle, array $options = [], $template = 'Ice\Core\Widget_Form_Radiobutton')
    {
        return $this->addPart($fieldName, $fieldTitle, $options, $template, Widget_Form::FIELD_RADIOBUTTON);
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
    public function combobox($fieldName, $fieldTitle, array $options = [], $template = 'Ice\Core\Widget_Form_Combobox')
    {
        return $this->addPart($fieldName, $fieldTitle, $options, $template, Widget_Form::FIELD_COMBOBOX);
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
    public function map($fieldName, $fieldTitle, array $options = [], $template = 'Ice\Core\Widget_Form_Map')
    {
        return $this->addPart($fieldName, $fieldTitle, $options, $template, Widget_Form::FIELD_MAP);
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
    public function textarea($fieldName, $fieldTitle, array $options = [], $template = 'Ice\Core\Widget_Form_Textarea')
    {
        return $this->addPart($fieldName, $fieldTitle, $options, $template, Widget_Form::FIELD_TEXTAREA);
    }

    public function file($fieldName, $fieldTitle, array $options = [], $template = 'Ice\Core\Widget_Form_File')
    {
        return $this->addPart($fieldName, $fieldTitle, $options, $template, Widget_Form::FIELD_FILE);
    }

    /**
     * Validate form by validate scheme
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.0
     * @since   0.0
     */
    public function validate()
    {
        return Validator::validateByScheme($this->getValues(), $this->getValidateScheme());
    }

    /**
     * Return validate scheme
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.0
     * @since   0.0
     */
    public function getValidateScheme()
    {
        $filterParts = $this->getFilterParts();

        return empty($filterParts)
            ? $this->validateScheme
            : array_intersect_key($this->validateScheme, array_flip($filterParts));
    }

    public function setQueryResult(Query_Result $queryResult)
    {
    }

    public static function create($url, $action, $block = null, array $data = [])
    {
        $form = parent::create($url, $action, $block, $data);

        /** @var Widget_Form $formClass */
        $formClass = get_class($form);

        $uploadTempDir = Module::getInstance()->get(Module::UPLOAD_TEMP_DIR) . '/' . $formClass::getClassName();

        foreach (array_keys($form->getParts()) as $key) {
            if (isset($params[$key])) {
                continue;
            }

            $path = implode('/', [$uploadTempDir, $key, $form->getToken()]);

            if (file_exists($path)) {
                $form->addValue($key, Directory::getFileNames($path));
            }
        }

        return $form;
    }

    /**
     *  protected static function config()
     *  {
     *      return [
     *          'view' => ['template' => null, 'viewRenderClass' => null, 'layout' => null],
     *          'input' => ['author' => 'request'],
     *          'access' => ['roles' => [], 'request' => null, 'env' => null]
     *      ];
     *  }
     *
     * @param Query_Builder $queryBuilder
     */
    public function queryBuilderPart(Query_Builder $queryBuilder)
    {
    }

    /**
     * @param string $onsubmit
     * @return Widget_Form
     */
    public function setOnsubmit($onsubmit)
    {
        $this->onsubmit = $onsubmit;
        return $this;
    }
}
