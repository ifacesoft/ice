<?php
/**
 * Ice core view class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

use Ice;
use Ice\Core;
use Ice\Data\Provider\Cacher;
use Ice\Helper\Emmet;

/**
 * Class View
 *
 * Core view class
 *
 * @see Ice\Core\Container
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Core
 */
class View implements Cacheable
{
    use Core;

    /**
     * Action class
     *
     * @var Action
     */
    private $_actionClass = null;

    /**
     * View render class
     *
     * @var View_Render
     */
    private $_viewRenderClass = null;

    /**
     * View template
     *
     * @var string
     */
    private $_template = null;

    /**
     * View layout
     *
     * @var string
     */
    private $_layout = null;

    /**
     * View render result
     *
     * @var string
     */
    private $_result = [
        'actionName' => '',
        'data' => [],
        'error' => '',
        'success' => '',
        'redirect' => '',
        'content' => ''
    ];

    /**
     * @param Action $actionClass
     */
    private function __construct($actionClass)
    {
        $this->_actionClass = $actionClass;
        $this->_result['actionName'] = $actionClass::getClassName();
    }

    /**
     * Return new instance of view
     *
     * @param $actionClass
     * @return View
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.0
     */
    public static function create($actionClass)
    {
        return new View($actionClass);
    }

    /**
     * Return view cacher
     *
     * @return Cacher
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.5
     */
    public static function getCacher()
    {
        return Cacher::getInstance(__CLASS__);
    }

    /**
     * Restore object
     *
     * @param array $data
     * @return object
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.5
     */
    public static function __set_state(array $data)
    {
        $class = self::getClass();

        $object = new $class($data['_actionClass']);

        foreach ($data as $fieldName => $fieldValue) {
            $object->$fieldName = $fieldValue;
        }

        return $object;
    }

    /**
     * Magic render view
     *
     * @see View::getContent()
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.0
     */
    function __toString()
    {
        return $this->getContent();
    }

    public function getContent()
    {
        return $this->getResult()['content'];
    }

    /**
     * @return string
     */
    public function getResult()
    {
        return $this->_result;
    }

    /**
     * Validate cacheable object
     *
     * @param $value
     * @return Cacheable
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since 0
     */
    public function validate($value)
    {
        return $this;
    }

    /**
     * Invalidate cacheable object
     *
     * @return Cacheable
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since 0
     */
    public function invalidate()
    {
        return $this;
    }

    public function render()
    {
        $startTime = Logger::microtime();

        if (empty($this->_template)) {
            return;
        }

        $viewRenderClass = $this->_viewRenderClass;

        array_unshift(View_Render::$templates, $this->_template);

        try {
            $this->_result['content'] = $viewRenderClass::getInstance()->fetch($this->_template, $this->_result['data']);


            if (!empty($this->_layout)) {
                $emmetedResult = Emmet::translate($this->_layout . '{{$view}}', ['view' => $this->_result['content']]);

                if (empty($emmetedResult)) {
                    $this->_result['content'] = $this->getLogger()->error(['Defined emmet layout string "{$0}" is corrupt', $this->_layout], __FILE__, __LINE__);
                }

                $this->_result['content'] = $emmetedResult;
            }

            if (Environment::isDevelopment()) {
                Logger::fb('view: ' . $this->_template . ' (' . $viewRenderClass . ') [' . Logger::microtimeResult($startTime) . ']');
            }
            array_shift(View_Render::$templates);
        } catch (\Exception $e) {
            $this->_result['content'] = $this->getLogger()->error(['Fetch template "{$0}" failed', $this->_template], __FILE__, __LINE__, $e);

            array_shift(View_Render::$templates);
        }
    }

    /**
     * @param string $template
     */
    public function setTemplate($template)
    {
        $actionClass = $this->_actionClass;

        if ($template === null) {
            $template = $actionClass::getConfig()->get('view/template', false);

            if ($template === null) {
                $this->_template = $actionClass;
                return;
            }
        }

        if ($template === '') {
            $this->_template = $template;
            return;
        }

        if ($template[0] == '_') {
            $this->_template = $actionClass . $template;
            return;
        }

        $this->_template = $template;
    }

    /**
     * @param View_Render $viewRenderClass
     */
    public function setViewRenderClass($viewRenderClass)
    {
        if (!$viewRenderClass) {
            $actionClass = $this->_actionClass;
            $viewRenderClass = $actionClass::getConfig()->get('view/viewRenderClass', false);
        }

        $this->_viewRenderClass = View_Render::getClass($viewRenderClass);
    }

    /**
     * @param string $layout
     */
    public function setLayout($layout)
    {
        $actionClass = $this->_actionClass;

        if ($layout === null) {
            $layout = $actionClass::getConfig()->get('view/layout', false);

            if ($layout === null) {
                $this->_layout = 'div.' . $this->getActionName();
                return;
            }
        }

        if ($layout === '') {
            $this->_layout = $layout;
            return;
        }

        if ($layout[0] == '_') {
            $this->_layout = 'div.' . $this->getActionName() . $layout;
            return;
        }

        $this->_layout = $layout;
    }

    public function setData($output)
    {
        $this->_result['data'] = $output;
    }

    public function getData()
    {
        return $this->getResult()['data'];
    }

    public function getActionName() {
        return $this->getResult()['actionName'];
    }
}