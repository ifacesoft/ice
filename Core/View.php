<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 07.12.13
 * Time: 11:29
 */

namespace ice\core;

use ice\core\helper\Object;
use ice\Exception;
use ice\Ice;

class View
{
    private $_viewRenderClass = null;
    private $_actionName = null;
    private $_template = null;
    private $_layout = null;
    private $_data = array();
    private $_view = null;

    public function __construct($actionClass, $layout)
    {
        $this->_actionName = Object::getName($actionClass);
        $this->_layout = $layout;
    }

    public function getTemplate()
    {
        if ($this->_template === null) {
            $this->_template = $this->_actionName;
        }

        return str_replace(array('_', '::'), '/', $this->_template);
    }

    public function setTemplate($template)
    {
        $this->_template = $template;
    }

    /**
     * @param string $layout
     */
    public function setLayout($layout)
    {
        $this->_layout = $layout;
    }

    /**
     * @return string
     */
    public function getLayout()
    {
        if ($this->_layout === null) {
            $this->_layout = 'div#' . $this->_actionName . '{$view}';
        }

        return $this->_layout;
    }

    /**
     * @param null $viewRenderClass
     */
    public function setViewRenderClass($viewRenderClass)
    {
        $this->_viewRenderClass = $viewRenderClass;
    }

    /**
     * @return string
     */
    public function getViewRenderClass()
    {
        if ($this->_viewRenderClass) {
            return $this->_viewRenderClass;
        }

        return Ice::getConfig()->getParam('defaultViewRenderClass');
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->_data;
    }

    public function setData(array $data)
    {
        $this->_data = $data;
    }

    /**
     * Получить результат рендера шаблона
     *
     * @throws Exception
     * @return string
     */
    public function render()
    {
        if ($this->_view !== null) {
            return $this->_view;
        }

        try {
            $this->_view = $this->fetch();
        } catch (\Exception $e) {
            $this->_view = '';
            Logger::outputErrors(
                new Exception('Не удалось отрендерить шаблон "' . $this->_template . '"', $e)
            );
        }


        return $this->_view;
    }

    private function fetch()
    {
        $template = $this->getTemplate();

        if (empty($template)) {
            return '';
        }

        /** @var View_Render $viewRenderClass */
        $viewRenderClass = $this->getViewRenderClass();

        return $viewRenderClass::get()->fetch($template, $this->getData(), $viewRenderClass::TEMPLATE_EXTENTION);
    }

    public function assign($key, $value)
    {
        $this->_data[$key] = $value;
    }

    public function display()
    {
        $template = $this->getTemplate();

        if (empty($template)) {
            return '';
        }

        /** @var View_Render $viewRenderClass */
        $viewRenderClass = $this->getViewRenderClass();

        $viewRenderClass::get()->display($template, $this->getData(), $viewRenderClass::TEMPLATE_EXTENTION);
    }
}