<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 20.02.14
 * Time: 1:17
 */

namespace ice\core;

use ice\core\helper\Object;

class Action_Context
{

    /**
     * @var array
     */
    private $_actions = array();
    private $_actionClass = null;
    /**
     * @var View
     */
    private $_view;
    /**
     * @var array
     */
    private $_dataProviderKeys = array();

    function __construct($actionClass, array $actions, $layout)
    {
        $this->_actionClass = $actionClass;

        $this->addAction($actions);

        $this->_view = new View($actionClass, $layout);

        if (in_array('ice\core\action\Ajaxable', class_implements($actionClass))) {
            $this->setTemplate('');
        }

        if (in_array('ice\core\action\Cliable', class_implements($actionClass))) {
            $this->addDataProviderKeys('Cli:prompt/');
            $this->setTemplate('');
        }
    }

    /**
     * @param array $dataProviderKeys
     */
    public function addDataProviderKeys($dataProviderKeys)
    {
        $this->_dataProviderKeys = array_merge($this->_dataProviderKeys, (array)$dataProviderKeys);
    }

    /**
     * @return array
     */
    public function getDataProviderKeys()
    {
        return $this->_dataProviderKeys;
    }

    public function setTemplate($template)
    {
        $this->_view->setTemplate($template);
    }

    /**
     * @return View
     */
    public function getView()
    {
        return $this->_view;
    }

    public function setData($output)
    {
        $this->_view->setData($output);
    }

    public function getData()
    {
        return $this->_view->getData();
    }

    public function getActions()
    {
        return $this->_actions;
    }

    public function assign($actionClass, $data)
    {
        $this->_view->assign(Object::getName($actionClass), $data);
    }

    public function setViewRenderClass($viewRenderClass)
    {
        $this->_view->setViewRenderClass($viewRenderClass);
    }

    public function addAction($actionName, array $params = array(), $key = null)
    {
        if (is_array($actionName)) {
            foreach ($actionName as $actionKey => $actionData) {
                if (!is_array($actionData)) {
                    $this->addAction($actionData);
                    continue;
                }

                $this->addAction($actionKey, $actionData);
            }

            return;
        }

        if (!isset($this->_actions[$actionName])) {
            $this->_actions[$actionName] = array();
        }

        if ($key) {
            $this->_actions[$actionName][$key] = $params;
        } else {
            array_unshift($this->_actions[$actionName], $params);
        }
    }
} 