<?php

namespace Ice\Widget;

use Ice\Core\Exception;
use Ice\Core\Widget;
use Ice\Exception\Config_Error;
use Ice\Exception\Error;
use Ice\Exception\FileNotFound;
use Ice\Exception\Http;
use Ice\Helper\Directory;
use Ice\Helper\Json;
use Ice\WidgetComponent\Form_Date;
use Ice\WidgetComponent\Form_ListBox;
use Ice\WidgetComponent\Form_Model_ManyToMany;
use Ice\WidgetComponent\Form_Model_OneToMany;
use Ice\WidgetComponent\Form_Model_OneToManyToMany;
use Ice\WidgetComponent\Form_Period;
use Ice\WidgetComponent\FormElement;
use Ice\WidgetComponent\FormElement_Button;
use Ice\WidgetComponent\FormElement_Chosen;
use Ice\WidgetComponent\FormElement_TextInput;
use Ice\WidgetComponent\Html_Form_InputFile;

class Form extends Widget
{
    /**
     * Field type map
     *
     * @var array
     */
    public static $typeMap = [
        'int' => 'number',
        'varchar' => 'text',
        'datetime' => 'date',
        'timestamp' => 'date',
        'tinyint' => 'checkbox',
        'point' => 'map',
        'bigint' => 'number',
        'text' => 'textarea',
        'double' => 'text',
        'longtext' => 'textarea'
    ];

    private $submitComponentName = null;

    /**
     * Form constructor.
     * @param array $data
     * @throws Exception
     * @throws Http
     */
    public function __construct(array $data)
    {
        parent::__construct($data);

        /** @var Form $formClass */
        $formClass = get_class($this);

        $tempDir = \getTempDir() . '/' . $formClass::getClassName();

        foreach (array_keys($this->getParts($this->getFilterParts())) as $key) {
            if (isset($params[$key])) {
                continue;
            }

            $path = implode('/', [$tempDir, $key, $this->getToken()]);

            if (file_exists($path)) {
                $this->set([$key => Directory::getFileNames($path)]);
            }
        }
    }

    /**
     * @param null $filterParts
     * @return \Ice\Core\WidgetComponent[]|FormElement[]
     */
    public function getParts($filterParts = null)
    {
        return parent::getParts($filterParts);
    }

    public static function schemeColumnOptions($columnName, $table, $tablePrefixes)
    {
        return [
            'type' => isset(Form::$typeMap[$table['columns'][$columnName]['scheme']['dataType']])
                ? Form::$typeMap[$table['columns'][$columnName]['scheme']['dataType']]
                : 'text'
        ];
    }

    /**
     * Widget config
     *
     * @return array
     */
    protected static function config()
    {
        return [
            'render' => ['template' => __CLASS__, 'class' => 'Ice:Php', 'layout' => null, 'resource' => true],
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => ''],
            'resource' => ['js' => null, 'css' => null, 'less' => null, 'img' => null],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'input' => [],
            'output' => [],
        ];
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
     * @throws Exception
     */
    public function text($fieldName, array $options = [], $template = 'Ice\Widget\Form\Text')
    {
        return $this->addPart(new FormElement_TextInput($fieldName, $options, $template, $this));
    }

    /**
     * @param $fieldName
     * @param array $options
     * @param string $template
     * @return Form
     * @throws Exception
     */
    public function html($fieldName, array $options = [], $template = 'Ice\Widget\Form\Html')
    {
        return $this->addPart(new FormElement_TextInput($fieldName, $options, $template, $this));
    }

    public function span($fieldName, array $options = [], $template = 'Ice\Widget\Form\Span')
    {
        return $this->addPart(new FormElement_TextInput($fieldName, $options, $template, $this));
    }

    /**
     * Add hidden type field
     *
     * @param $fieldName
     * @param  array $options
     * @param  string $template
     * @return $this
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   0.0
     * @throws Exception
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
     * @throws Exception
     */
    public function password($fieldName, array $options = [], $template = 'Ice\Widget\Form\Password')
    {
        return $this->addPart(new FormElement_TextInput($fieldName, $options, $template, $this));
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
     * @throws Exception
     */
    public function number($fieldName, array $options = [], $template = 'Ice\Widget\Form\Number')
    {
        return $this->addPart(new FormElement_TextInput($fieldName, $options, $template, $this));
    }

    /**
     * Add date type field
     *
     * @param $fieldName
     * @param  array $options
     * @param null $template
     * @return Form
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   0.0
     * @throws Exception
     */
    public function date($fieldName, array $options = [], $template = null)
    {
        return $this->addPart(new Form_Date($fieldName, $options, $template, $this));
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
     * @throws Exception
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
     * @throws Exception
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
     * @throws Exception
     * @deprecated use chosen
     */
    public function combobox($fieldName, array $options = [], $template = 'Ice\Widget\Form\Combobox')
    {
        return $this->addPart(new FormElement($fieldName, $options, $template, $this));
    }

    /**
     * Add combobox type field
     *
     * @param  $fieldName
     * @param  array $options
     * @return Form
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   1.1
     * @throws Exception
     */
    public function listbox($fieldName, array $options = [])
    {
        return $this->addPart(new Form_ListBox($fieldName, $options, null, $this));
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
     * @throws Exception
     */
    public function chosen($fieldName, array $options = [], $template = null)
    {
        return $this->addPart(new FormElement_Chosen($fieldName, $options, $template, $this));
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
     * @throws Exception
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
     * @throws Exception
     */
    public function textarea($fieldName, array $options = [], $template = 'Ice\Widget\Form\Textarea')
    {
        return $this->addPart(new FormElement_TextInput($fieldName, $options, $template, $this));
    }

    /**
     * @param $fieldName
     * @param array $options
     * @return Form
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   1.0
     * @throws Exception
     */
    public function file($fieldName, array $options = [])
    {
        return $this->addPart(new Html_Form_InputFile($fieldName, $options, null, $this));
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
     * @throws Exception
     */
    public function period($fieldName, array $options = [], $template = null)
    {
        return $this->addPart(new Form_Period($fieldName, $options, $template, $this));
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
     * @throws Exception
     */
    public function button($fieldName, array $options = [], $template = 'Ice\Widget\Form\Button')
    {
        return $this->addPart(new FormElement_Button($fieldName, $options, $template, $this));
    }

    /**
     * @param $fieldName
     * @param array $options
     * @return $this
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   1.1
     * @throws Exception
     */
    public function oneToMany($fieldName, array $options = [])
    {
        return $this->addPart(new Form_Model_OneToMany($fieldName, $options, null, $this));
    }

    /**
     * @param $fieldName
     * @param array $options
     * @return $this
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   1.1
     * @throws Exception
     */
    public function manyToMany($fieldName, array $options = [])
    {
        return $this->addPart(new Form_Model_ManyToMany($fieldName, $options, null, $this));
    }

    /**
     * @param $fieldName
     * @param array $options
     * @return $this
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   1.1
     * @throws Exception
     */
    public function oneToManyToMany($fieldName, array $options = [])
    {
        return $this->addPart(new Form_Model_OneToManyToMany($fieldName, $options, null, $this));
    }

    /**
     * @param $fieldName
     * @param array $options
     * @return $this
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   1.1
     * @throws Exception
     */
    public function manyToOne($fieldName, array $options = [])
    {
        return $this->addPart(new FormElement_Chosen($fieldName, $options, null, $this));
    }

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

    /**
     * @param null $submitComponentName
     */
    public function setSubmitComponentName($submitComponentName)
    {
        $this->submitComponentName = $submitComponentName;
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

    /**
     * @return array|null
     * @throws Exception
     * @throws Error
     * @throws \Exception
     */
    protected function getCompiledResult()
    {
        $compiledResult = parent::getCompiledResult();

        /** @var FormElement $component */
        $component = $this->getPart($this->submitComponentName);

        if (!$component) {
            return array_merge(
                $compiledResult,
                ['onSubmit' => '', 'url' => '/', 'method' => 'POST']
            );
        }

        return array_merge(
            $compiledResult,
            [
                'dataAction' => $component->getDataAction(),
                'dataParams' => base64_encode(Json::encode(array_filter($this->encodedGet()))),
                'onSubmit' => $component->getEvent()['ajax'] ? $component->getEventCode() : '',
                'url' => $component->getHref(),
                'method' => $component->getRoute() ? $component->getRoute()['method'] : 'POST'
            ]
        );
    }

    /**
     * @param null $timeout
     * @param null $routeRedirect
     * @return Form
     * @throws Exception
     */
    public function divMessage($timeout = null, $routeRedirect = null)
    {
        if ($timeout !== null) {
            $this->setTimeout($timeout);
        }

        if ($routeRedirect !== null) {
            $this->setRedirect($routeRedirect);
        }

        return $this->div('ice-message', ['valueKey' => '', 'encode' => false, 'resource' => false]);
    }

    /**
     * @return array
     * @throws Exception
     * @throws Config_Error
     * @throws Error
     * @throws FileNotFound
     */
    protected function encodedGet()
    {
        $get = [];

        foreach ($this->get() as $name => $value) {
            $get[$name] = is_string($value) ? htmlentities($value) : $value;
        }

        return $get;
    }
}