<?php
/**
 * Ice core action context class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
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
 * @package    Ice
 * @subpackage Core
 *
 * @deprecated 2.0
 * @version 0.2
 * @since   0.0
 */
class Action_Context
{
    use Core;

    /**
     * Action full stack
     *
     * @var array
     */
    private $fullStack = [];

    /**
     * String temp content current action
     *
     * @var string
     */
    private $tempContent = null;

    private function __construct()
    {
    }

    public static function create()
    {
        return new Action_Context();
    }

    /**
     * Initialize added action (push to full action call stack)
     *
     * @param  string $actionClass
     * @param  $hash
     * @return Action_Context
     * @throws Exception
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function initAction($actionClass, $hash)
    {
        if (!isset($this->fullStack[$actionClass])) {
            $this->fullStack[$actionClass] = [];
        }

        if (!isset($this->fullStack[$actionClass][$hash])) {
            $this->fullStack[$actionClass][$hash] = 0;
        }

        if ($this->fullStack[$actionClass][$hash] < 5) {
            $this->fullStack[$actionClass][$hash]++;

            return $this;
        }

        Logger::getInstance(__CLASS__)->exception(
            [
                'Action {$0} with input hash {$1} already runned ({$2}). May by found infinite loop.',
                [$actionClass, $hash, $this->fullStack[$actionClass][$hash]]
            ],
            __FILE__,
            __LINE__,
            null,
            $this->fullStack
        );

        return $this;
    }

    /**
     * Return action full stack
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function getFullStack()
    {
        return $this->fullStack;
    }

    /**
     * Get current content
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
     */
    public function getContent()
    {
        return $this->tempContent;
    }

    /**
     * Set current content
     *
     * @param mixed $content
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
     */
    public function setContent($content)
    {
        $this->tempContent = $content;
    }
}
