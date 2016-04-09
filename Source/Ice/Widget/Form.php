<?php

namespace Ice\Widget;

use Ice\Core\Action;
use Ice\Core\Module;
use Ice\Core\Validator;
use Ice\Core\Widget;
use Ice\Helper\Directory;
use Ice\WidgetComponent\FormElement;
use Ice\WidgetComponent\FormElement_Button;
use Ice\WidgetComponent\FormElement_Period;
use Ice\WidgetComponent\FormElement_Text;

class Form extends Widget
{
    /**
     * Validate scheme for validate fields
     *
     * @var array
     */
    protected $validateScheme = [];

    private $submitPartName = null;

    public function __construct(array $data)
    {
        parent::__construct($data);

        /** @var Form $formClass */
        $formClass = get_class($this);

        $tempDir = Module::getInstance()->get(Module::TEMP_DIR) . '/' . $formClass::getClassName();

        foreach (array_keys($this->getParts($this->getFilterParts())) as $key) {
            if (isset($params[$key])) {
                continue;
            }

            $path = implode('/', [$tempDir, $key, $this->getToken()]);

            if (file_exists($path)) {
                $this->bind([$key => Directory::getFileNames($path)]);
            }
        }
    }

    /**
     * Widget config
     *
     * @return array
     */
    protected static function config()
    {
        return [
            'render' => ['template' => __CLASS__, 'class' => 'Ice:Php', 'layout' => null, 'resource' => null],
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => 'Widget: Access denied!'],
            'resource' => ['js' => null, 'css' => null, 'less' => null, 'img' => null],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'input' => [],
            'output' => [],
        ];
    }

    /** Build widget
     *
     * @param array $input
     * @return array
     */
    protected function build(array $input)
    {
        return [];
    }

    protected function getCompiledResult()
    {
        $submitOptions = $this->submitPartName ? $this->getPart($this->submitPartName)['options'] : null;

        if ($submitOptions && !empty($submitOptions['params'])) {
            $this->setDataParams(array_merge($this->getDataParams(), $submitOptions['params']));
        }

        return array_merge(
            parent::getCompiledResult(),
            [
                'dataAction' => $submitOptions['dataAction'],
                'onSubmit' => $submitOptions['submit'],
                'url' => $submitOptions['url'],
                'method' => $submitOptions['method']
            ]
        );
    }

    public static function schemeColumnPlugin($columnName, $table)
    {
        return isset(Model_Form::$typeMap[$table['columns'][$columnName]['scheme']['dataType']])
            ? Model_Form::$typeMap[$table['columns'][$columnName]['scheme']['dataType']]
            : 'text';
    }

    /**
     * Add text type field
     *
     * @param  $fieldName
     * @param  array $options
     * @param  string $template
     * @return Form
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   0.0
     */
    public function text($fieldName, array $options = [], $template = 'Ice\Widget\Form\Text')
    {
        return $this->addPart(new FormElement_Text($fieldName, $options, $template, $this));
    }

    public function html($fieldName, array $options = [], $template = 'Ice\Widget\Form\Html')
    {
        return $this->addPart(new FormElement($fieldName, $options, $template, $this));
    }

    /**
     * Add hidden type field
     *
     * @param $fieldName
     * @param  array $options
     * @param  string $template
     * @return Form
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   0.0
     */
    public function hidden($fieldName, array $options = [], $template = 'Ice\Widget\Form\Hidden')
    {
        return $this->addPart(new FormElement($fieldName, $options, $template, $this));
    }

    /**
     * Add password type field
     *
     * @param  $fieldName
     * @param  array $options
     * @param  string $template
     * @return Form
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   0.1
     */
    public function password($fieldName, array $options = [], $template = 'Ice\Widget\Form\Password')
    {
        return $this->addPart(new FormElement($fieldName, $options, $template, $this));
    }

    /**
     * Add number type field
     *
     * @param  $fieldName
     * @param  array $options
     * @param  string $template
     * @return Form
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   0.0
     */
    public function number($fieldName, array $options = [], $template = 'Ice\Widget\Form\Number')
    {
        return $this->addPart(new FormElement($fieldName, $options, $template, $this));
    }

    /**
     * Add date type field
     *
     * @param  $fieldName
     * @param  array $options
     * @param  string $template
     * @return Form
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   0.0
     */
    public function date($fieldName, array $options = [], $template = 'Ice\Widget\Form\Date')
    {
        return $this->addPart(new FormElement($fieldName, $options, $template, $this));
    }

    /**
     * Add checkbox type field
     *
     * @param  $fieldName
     * @param  array $options
     * @param  string $template
     * @return Form
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   0.0
     */
    public function checkbox($fieldName, array $options = [], $template = 'Ice\Widget\Form\Checkbox')
    {
        return $this->addPart(new FormElement($fieldName, $options, $template, $this));
    }

    /**
     * Add radio button type field
     *
     * @param  $fieldName
     * @param  array $options
     * @param  string $template
     * @return Form
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   0.0
     */
    public function radio($fieldName, array $options = [], $template = 'Ice\Widget\Form\Radiobutton')
    {
        return $this->addPart(new FormElement($fieldName, $options, $template, $this));
    }

    /**
     * Add combobox type field
     *
     * @param  $fieldName
     * @param  array $options
     * @param  string $template
     * @return Form
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   0.0
     */
    public function combobox($fieldName, array $options = [], $template = 'Ice\Widget\Form\Combobox')
    {
        return $this->addPart(new FormElement($fieldName, $options, $template, $this));
    }

    /**
     * Add choseh type field
     *
     * Required "harvesthq/bower-chosen" package
     *
     * Check composer.json:
     * ```json
     *  "repositories": [
     *      {
     *          "type": "package",
     *          "package": {
     *              "name": "harvesthq/bower-chosen",
     *              "version": "1.4.2",
     *              "source": {
     *                  "type": "git",
     *                  "url": "https://github.com/harvesthq/bower-chosen.git",
     *                  "reference": "1.4.2"
     *              }
     *          }
     *      }
     *  ],
     *  "require": {
     *      "harvesthq/bower-chosen": "1.4.2"
     *  },
     * ```
     *
     * @param  $fieldName
     * @param  array $options
     * @param  string $template
     * @return Form
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   1.1
     */
    public function chosen($fieldName, array $options = [], $template = 'Ice\Widget\Form\Chosen')
    {
        return $this->addPart(new FormElement($fieldName, $options, $template, $this));
    }

    /**
     * Add map type field
     *
     * @param  $fieldName
     * @param  array $options
     * @param  string $template
     * @return Form
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   0.0
     */
    public function map($fieldName, array $options = [], $template = 'Ice\Widget\Form\Map')
    {
        return $this->addPart(new FormElement($fieldName, $options, $template, $this));
    }

    /**
     * Add textarea type field
     *
     * @param  $fieldName
     * @param  array $options
     * @param  string $template
     * @return Form
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   0.0
     */
    public function textarea($fieldName, array $options = [], $template = 'Ice\Widget\Form\Textarea')
    {
        return $this->addPart(new FormElement($fieldName, $options, $template, $this));
    }

    /**
     * @param $fieldName
     * @param array $options
     * @param string $template
     * @return Form
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   1.0
     */
    public function file($fieldName, array $options = [], $template = 'Ice\Widget\Form\File')
    {
        return $this->addPart(new FormElement($fieldName, $options, $template, $this));
    }

    /**
     * @param $fieldName
     * @param array $options
     * @param string $template
     * @return Form
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   1.1
     */
    public function period($fieldName, array $options = [], $template = 'Ice\Widget\Form\Period')
    {
        return $this->addPart(new FormElement_Period($fieldName, $options, $template, $this));
    }

    /**
     * @param $fieldName
     * @param array $options
     * @param string $template
     * @return Form
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   1.0
     */
    public function button($fieldName, array $options = [], $template = 'Ice\Widget\Form\Button')
    {
        return $this->addPart(new FormElement_Button($fieldName, $options, $template, $this));
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

//    protected function addPart($partName, array $options, $template, $element)
//    {
//        parent::addPart($partName, $options, $template, $element);
//
//        if (!empty($this->parts[$partName]['options']['validators'])) {
//            $this->validateScheme[$partName] = $this->parts[$partName]['options']['validators'];
//            unset($this->parts[$partName]['options']['validators']);
//        }
//
//        return $this;
//    }

    /**
     * @param int $offset input offset
     * @return $this
     */
    public function setHorizontal($offset = 2)
    {
        $this->addClasses('form-horizontal');
        $this->setOption('horizontal', $offset);

        return $this;
    }
}