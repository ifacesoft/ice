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

    private function __construct()
    {
    }

    public static function create()
    {
        return new Action_Context();
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
     * Initialize added action (push to full action call stack)
     *
     * @param string $actionClass
     * @param $hash
     * @return Action_Context
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

            $this->_viewData[$inputHash] = [
                'actionClass' => $actionClass,
                'layout' => $config->get('layout', false),
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