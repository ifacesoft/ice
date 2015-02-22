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
use Ice\Helper\Object;

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

    private $_viewRenderClass = null;
    private $_template = null;
    private $_layout = null;
    private $_params = [];

    /**
     * Rendered view
     *
     * @var string
     */
    private $_result = null;

    function __construct($viewRenderClass, $template, $layout, $params)
    {
        $this->_viewRenderClass = $viewRenderClass;
        $this->_template = $template;
        $this->_layout = $layout;
        $this->_params = $params;
    }

    /**
     * Return new instance of view
     *
     * @param Action $action
     * @return View
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.0
     */
    public static function create(Action $action)
    {
        return new View($action->getViewRenderClass(), $action->getTemplate(), $action->getLayout(), $action->getOutput());
    }

    /**
     * Render view
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getContent()
    {
        $startTime = Logger::microtime();

        if ($this->_result != null) {
            return $this->_result;
        }

        $template = $this->getTemplate();

        if (empty($template)) {
            return $this->_result = '';
        }

        /** @var View_Render $viewRenderClass */
        $viewRenderClass = $this->getViewRenderClass();

        array_unshift(View_Render::$templates, $template);

        try {
            $this->_result = $viewRenderClass::getInstance()->fetch($template, $this->_params);

            $layout = $this->getLayout();

            if (!empty($layout)) {
                $emmetedResult = Emmet::translate($this->getLayout(), ['view' => $this->_result]);

                if (empty($emmetedResult)) {
                    $this->_result = $this->getLogger()->error(['Defined emmet layout string "{$0}" is corrupt', $this->getLayout()], __FILE__, __LINE__);
                }

                $this->_result = $emmetedResult;
            }

            if (Environment::isDevelopment()) {
                Logger::fb('view: ' . $template . ' (' . $viewRenderClass . ') [' . Logger::microtimeResult($startTime) . ']');
            }
        } catch (\Exception $e) {
            $this->_result = $this->getLogger()->error(['Fetch template "{$0}" failed', $template], __FILE__, __LINE__, $e);
        }

        array_shift(View_Render::$templates);

        return $this->_result;
    }

    /**
     * Return view template name
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getTemplate()
    {
        if ($this->_template === '') {
            return $this->_template;
        }

        $actionClass = $this->_viewData['actionClass'];

        if ($this->_template === null) {
            $this->_template = $actionClass;
        }

        if ($this->_template[0] == '_') {
            $this->_template = $actionClass . $this->_template;
        }

        return $this->_template;
    }

    /**
     * Return view render class
     *
     * @return View_Render
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.0
     */
    public function getViewRenderClass()
    {
        $viewRenderClassName = isset($this->_viewData['defaultViewRenderClassName'])
            ? $this->_viewData['defaultViewRenderClassName']
            : View::getConfig()->get('defaultViewRenderClassName');

        return Object::getClass(View_Render::getClass(), $viewRenderClassName);
    }

    /**
     * Return emmet style layout
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getLayout()
    {
        $postfix = '';

        if (!empty($this->_viewData['layout'])) {
            if (!ctype_alpha($this->_viewData['layout'][0])) {
                $postfix = $this->_viewData['layout'];
                $this->_viewData['layout'] = null;
            } else {
                return $this->_viewData['layout'] . '{{$view}}';
            }
        }

        $actionClass = $this->_viewData['actionClass'];

        if (Request::isCli() || View::getConfig()->get('defaultLayoutView', false) === '') {
            $this->_viewData['layout'] = '';
        }

        if ($this->_viewData['layout'] === '') {
            return $this->_viewData['layout'];
        }

        $this->_viewData['layout'] = 'div.' . Object::getName($actionClass) . $postfix;

        return $this->_viewData['layout'] . '{{$view}}';
    }

    /**
     * Assign params
     *
     * @param $params
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function setParams($params)
    {
        $this->_viewData['params'] = $params;
    }

    /**
     * Return assigned params
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getParams()
    {
        return $this->_viewData['params'];
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
     * @version 0.0
     * @since 0.0
     */
    function __toString()
    {
        return $this->getContent();
    }

    /**
     * Return action class of view
     *
     * @return Action
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getActionClass()
    {
        return $this->_viewData['actionClass'];
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
        // TODO: Implement validate() method.
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
        // TODO: Implement invalidate() method.
    }
}