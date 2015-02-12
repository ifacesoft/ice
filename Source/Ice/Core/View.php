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
 *
 * @version 0.0
 * @since 0.0
 */
class View extends Container
{
    use Core;

    /**
     * View data (template, view render class etc.
     *
     * @var array
     */
    private $_viewData = [];

    /**
     * Rendered view
     *
     * @var string
     */
    private $_result = null;

    /**
     * Private constructor for core view
     *
     * @param array $viewData
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    private function __construct(array $viewData)
    {
        $this->_viewData = $viewData;
    }

    /**
     * Return new instance of view
     *
     * @param $viewKey
     * @return View
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    protected static function create($viewKey)
    {
        return new View($viewKey);
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

        $hash = crc32(serialize($this->_viewData));

        $dataProvider = View::getDataProvider();

        if ($this->_result = $dataProvider->get($hash)) {
            return $this->_result;
        }

        /** @var View_Render $viewRenderClass */
        $viewRenderClass = $this->getViewRenderClass();

        array_unshift(View_Render::$templates, $template);

        try {
            $this->_result = $viewRenderClass::getInstance()->fetch($template, $this->_viewData['params']);

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

            $dataProvider->set($hash, $this->_result);
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
        if ($this->_viewData['template'] === '') {
            return $this->_viewData['template'];
        }

        $actionClass = $this->_viewData['actionClass'];

        if ($this->_viewData['template'] === null) {
            $this->_viewData['template'] = $actionClass;
        }

        if ($this->_viewData['template'][0] == '_') {
            $this->_viewData['template'] = $actionClass . $this->_viewData['template'];
        }

        return $this->_viewData['template'];
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

}