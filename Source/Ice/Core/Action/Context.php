<?php
/**
 * Ice core action context class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

use Ice\Core;

/**
 * Class Action_Context
 *
 * Core Action context class
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Core
 *
 * @version 0.2
 * @since 0.0
 */
class Action_Context
{
    use Core;

    /**
     * Child Actions
     *
     * Will be runned after current action
     *
     * @var array
     */
    private $_actions = [];

    /**
     * Action call stack
     *
     * @var array
     */
    private $_stack = [];

    /**
     * Action full stack
     *
     * @var array
     */
    private $_fullStack = [];

    /**
     * Received view data after run action
     *
     * @var array
     */
    private $_viewData = [];

    /**
     * String temp content current action
     *
     * @var string
     */
    private $_tempContent = null;

    /**
     * Response
     *
     * @var Response
     */
    private $_response = null;

    private function __construct()
    {
    }

    public static function create()
    {
        return new Action_Context();
    }

    /**
     * Add child action
     *
     * 'Ice:Title',
     * 'title_template_var1' => 'Ice:Title',
     * 'Ice:Title' => ['title' => 'text'],
     * 'title_template_var2' => ['Ice:Title', ['title' => 'text']]
     *
     * @param $actionName
     * @param array $params
     * @param string|null $key
     * @return Action_Context
     * @throws Exception
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.0
     */
    public function addAction($actionName, array $params = [], $key = null)
    {
        if (empty($actionName)) {
            return;
        }

        if (is_array($actionName)) {
            foreach ($actionName as $actionKey => $actionData) {
                if (is_numeric($actionKey)) {
                    $this->addAction($actionData);
                    continue;
                }

                if (!is_array($actionData)) {
                    $this->addAction($actionData, [], $actionKey);
                    continue;
                }

                if (is_numeric(each($actionData)['key'])) {
                    foreach ($actionData as $data) {
                        $this->addAction($actionKey, $data);
                    }

                    continue;
                }

                $this->addAction($actionKey, $actionData, $key);
            }
            return;
        }

        if (!isset($this->_actions[$actionName])) {
            $this->_actions = array_merge([$actionName => []], $this->_actions);
        }

        if (is_string($key)) {
            $this->_actions[$actionName][$key] = $params;
        } else {
            $this->_actions[$actionName][] = $params;
        }
    }

    /**
     * Assign data to view
     *
     * @param array $params
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function setParams(array $params)
    {
        $this->_viewData[end($this->_stack)]['params'] = $params;
    }

    /**
     * Set runtime template
     *
     * @param string $template
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function setTemplate($template)
    {
        $this->_viewData[end($this->_stack)]['template'] = $template;
    }

    /**
     * Return child actions
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getActions()
    {
        return $this->_actions;
    }

    /**
     * Initialize added action (push to full action call stack)
     *
     * @param string $actionClass
     * @param $hash
     * @throws Exception
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function initAction($actionClass, $hash)
    {
        if (!isset($this->_fullStack[$actionClass])) {
            $this->_fullStack[$actionClass] = [];
        }

        if (!isset($this->_fullStack[$actionClass][$hash])) {
            $this->_fullStack[$actionClass][$hash] = 0;
        }

        if ($this->_fullStack[$actionClass][$hash] < 5) {
            $this->_fullStack[$actionClass][$hash]++;

            $inputHash = $actionClass . '/' . $hash;

            array_push($this->_stack, $inputHash);

            /** @var Action $actionClass */
            /** @var Config $config */
            $config = $actionClass::getConfig();

//            $this->_actions = $config->gets('afterActions', false);
            $this->_actions = [];

            $this->_viewData[$inputHash] = [
                'actionClass' => $actionClass,
                'layout' => $config->get('layout', false),
                'template' => $config->get('template', false),
                'output' => $config->get('output', false),
                'defaultViewRenderClassName' => $config->get('viewRenderClassName', false),
                'params' => []
            ];

            return $this;
        }

        Action::getLogger()->exception(['Action {$0} with input hash {$1} already runned ({$2}). May by found infinite loop.', [$actionClass, $hash, $this->_fullStack[$actionClass][$hash]]], __FILE__, __LINE__, null, $this->_fullStack);
    }

    /**
     * Return resulted view data
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getViewData()
    {
        return $this->_viewData[end($this->_stack)];
    }

    /**
     * Return action full stack
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getFullStack()
    {
        return $this->_fullStack;
    }

    /**
     * Pop action name from call stack
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function commit()
    {
        $inputHash = array_pop($this->_stack);
        unset($this->_viewData[$inputHash]);
    }

    /**
     * Return response by request
     *
     * @return Response
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    public function getResponse()
    {
        if ($this->_response !== null) {
            return $this->_response;
        }

        return $this->_response = Response::create();
    }

    /**
     * Get current content
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    public function getContent()
    {
        return $this->_tempContent;
    }

    /**
     * Set current content
     *
     * @param mixed $content
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    public function setContent($content)
    {
        $this->_tempContent = $content;
    }
}