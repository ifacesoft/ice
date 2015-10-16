<?php

namespace Ice\Widget;

use Ice\Core\Action;
use Ice\Core\Module;
use Ice\Core\Validator;
use Ice\Core\Widget;
use Ice\Helper\Directory;

abstract class Form extends Widget
{
    /**
     * Validate scheme for validate fields
     *
     * @var array
     */
    protected $validateScheme = [];

    private $horizontal = null;

    private $submit = null;

    private $action = '';

    private $method = 'POST';

    protected function getCompiledResult()
    {
        $submitOptions =  $this->submit ? $this->getPart($this->submit)['options'] : null;

        if ($submitOptions && !empty($submitOptions['params'])) {
            $this->setDataParams(array_merge($this->getDataParams(), $submitOptions['params']));
        }

        return array_merge(
            parent::getCompiledResult(),
            [
                'action' => $this->action,
                'method' => $this->method,
                'dataAction' => $submitOptions ? $submitOptions['dataAction'] : null,
                'onSubmit' => $submitOptions ? $submitOptions['submit'] : null
            ]
        );
    }

    /**
     * Init widget parts and other
     * @param array $input
     * @return array|void
     */
    public function init(array $input)
    {
        parent::init($input);

        /** @var Form $formClass */
        $formClass = get_class($this);

        $uploadTempDir = Module::getInstance()->get(Module::UPLOAD_TEMP_DIR) . '/' . $formClass::getClassName();

        foreach (array_keys($this->getParts($this->getFilterParts())) as $key) {
            if (isset($params[$key])) {
                continue;
            }

            $path = implode('/', [$uploadTempDir, $key, $this->getToken()]);

            if (file_exists($path)) {
                $this->bind([$key => Directory::getFileNames($path)]);
            }
        }
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
        if ($this->horizontal) {
            $options['horizontal'] = $this->horizontal;
        }

        return $this->addPart($fieldName, $options, $template, __FUNCTION__);
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
        return $this->addPart($fieldName, $options, $template, __FUNCTION__);
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
        if ($this->horizontal) {
            $options['horizontal'] = $this->horizontal;
        }

        return $this->addPart($fieldName, $options, $template, __FUNCTION__);
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
        if ($this->horizontal) {
            $options['horizontal'] = $this->horizontal;
        }

        return $this->addPart($fieldName, $options, $template, __FUNCTION__);
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
        if ($this->horizontal) {
            $options['horizontal'] = $this->horizontal;
        }

        return $this->addPart($fieldName, $options, $template, __FUNCTION__);
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
        if ($this->horizontal) {
            $options['horizontal'] = $this->horizontal;
        }

        return $this->addPart($fieldName, $options, $template, __FUNCTION__);
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
        if ($this->horizontal) {
            $options['horizontal'] = $this->horizontal;
        }

        return $this->addPart($fieldName, $options, $template, __FUNCTION__);
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
        if ($this->horizontal) {
            $options['horizontal'] = $this->horizontal;
        }

        return $this->addPart($fieldName, $options, $template, __FUNCTION__);
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
     * @version 2.0
     * @since   2.0
     */
    public function chosen($fieldName, array $options = [], $template = 'Ice\Widget\Form\Chosen')
    {
        if ($this->horizontal) {
            $options['horizontal'] = $this->horizontal;
        }

        return $this->addPart($fieldName, $options, $template, __FUNCTION__);
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
        return $this->addPart($fieldName, $options, $template, __FUNCTION__);
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
        if ($this->horizontal) {
            $options['horizontal'] = $this->horizontal;
        }

        return $this->addPart($fieldName, $options, $template, __FUNCTION__);
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
        return $this->addPart($fieldName, $options, $template, __FUNCTION__);
    }

    /**
     * @param null $submit
     */
    public function setSubmit($submit)
    {
        $this->submit = $submit;
    }

    /**
     * @param $fieldName
     * @param array $options
     * @param string $template
     * @return Form
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 2.0
     * @since   1.0
     */
    public function button($fieldName, array $options = [], $template = 'Ice\Widget\Form\Button')
    {
        if ($this->horizontal) {
            $options['horizontal'] = $this->horizontal;
        }

        if (!isset($options['resource'])) {
            $options['resource'] = get_class($this);
        }

        if (isset($options['submit'])) {
            $this->submit = $fieldName;
        }

        return $this->addPart($fieldName, $options, $template, __FUNCTION__);
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

    protected function addPart($partName, array $options, $template, $element)
    {
        parent::addPart($partName, $options, $template, $element);

        if (!empty($this->parts[$partName]['options']['validators'])) {
            $this->validateScheme[$partName] = $this->parts[$partName]['options']['validators'];
            unset($this->parts[$partName]['options']['validators']);
        }

        return $this;
    }

    /**
     * @param int $offset input offset
     * @return Form
     */
    public function setHorizontal($offset = 2)
    {
        $this->setClasses('form-horizontal');
        $this->horizontal = $offset;

        return $this;
    }
}