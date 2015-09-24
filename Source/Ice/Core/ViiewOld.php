<?php
/**
 * Ice core view class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

use Ice\Core;
use Ice\Data\Provider\Cacher;
use Ice\Helper\Emmet;
use Ice\Helper\Hash;
use Ice\Helper\Json;

/**
 * Class View
 *
 * Core view class
 *
 * @see Ice\Core\Container
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Core
 */
class ViiewOld implements Cacheable
{
    use Stored;

    /**
     * Action class
     *
     * @var Action
     */
    private $actionClass = null;

    /**
     * View render class
     *
     * @var Render
     */
    private $viewRenderClass = null;

    /**
     * View template
     *
     * @var string
     */
    private $template = null;

    /**
     * View layout
     *
     * @var string
     */
    private $layout = null;

    /**
     * View render result
     *
     * @var string
     */
    private $result = [
        'actionName' => '',
        'data' => [],
        'error' => '',
        'success' => '',
        'redirect' => '',
        'content' => ''
    ];

    /**
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.0
     */
    private function __construct()
    {
    }

    /**
     * Return new instance of view
     *
     * @param  Action $actionClass
     * @return ViiewOld
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since   0.0
     */
    public static function create($actionClass)
    {
        $view = new ViiewOld();

        if ($actionClass) {
            $view->actionClass = $actionClass;
            $view->result['actionName'] = $actionClass::getClassName();
        }

        return $view;
    }
//
//    /**
//     * Return view cacher
//     *
//     * @return Cacher
//     *
//     * @author dp <denis.a.shestakov@gmail.com>
//     *
//     * @version 0.5
//     * @since   0.5
//     */
//    public static function getCacher()
//    {
//        return Cacher::getInstance(__CLASS__);
//    }

//    /**
//     * Magic render view
//     *
//     * @see View::getContent()
//     *
//     * @return string
//     *
//     * @author dp <denis.a.shestakov@gmail.com>
//     *
//     * @version 0.5
//     * @since   0.0
//     */
//    public function __toString()
//    {
//        return $this->getContent();
//    }

    public function getContent()
    {
        return $this->getResult()['content'];
    }

    /**
     * @return string
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Validate cacheable object
     *
     * @param  $value
     * @return Cacheable
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
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
     * @since   0
     */
    public function invalidate()
    {
        return $this;
    }

    public function render()
    {
        $startTime = Profiler::getMicrotime();
        $startMemory = Profiler::getMemoryGetUsage();

        if (empty($this->template)) {
            return;
        }

        $viewRenderClass = $this->viewRenderClass;

        array_unshift(Render::$templates, $this->template);

        try {
            $cacher = ViiewOld::getCacher($this->template);
            $key = crc32(Json::encode($this->result['data']));

//            if ($this->result['content'] = $cacher->get($key)) {
//
//                Profiler::setPoint(
//                    $this->template . ' (' . $viewRenderClass::getClassName() . ')',
//                    $startTime,
//                    $startMemory
//                );
//
//                Logger::fb(
//                    Profiler::getReport($this->template . ' (' . $viewRenderClass::getClassName() . ')'),
//                    'view (cache)',
//                    'LOG'
//                );
//
//
//                array_shift(View_Render::$templates);
//                return;
//            }

            $this->result['content'] = $cacher->set(
                $key,
                $viewRenderClass::getInstance()->fetch($this->template, $this->result['data'])
            );

            //            if  (isset($this->_result['data']['data'])) {
            //                $this->setData($this->_result['data']['data']);
            //            } else {
            //                $this->setData([]);
            //            }

            if (!empty($this->layout)) {
                $emmetedResult = Emmet::translate($this->layout . '{{$view}}', ['view' => $this->result['content']]);

                if (empty($emmetedResult)) {
                    $this->result['content'] = $this->getLogger()->error(
                        ['Defined emmet layout string "{$0}" is corrupt', $this->layout],
                        __FILE__,
                        __LINE__
                    );
                }

                $this->result['content'] = $emmetedResult;
            }

            Profiler::setPoint($this->template . ' (' . $viewRenderClass::getClassName() . ')', $startTime, $startMemory);

            Logger::fb(
                Profiler::getReport($this->template . ' (' . $viewRenderClass::getClassName() . ')'),
                'view (new)',
                'INFO'
            );

            array_shift(Render::$templates);
        } catch (\Exception $e) {
            echo ($e->getMessage());

            $this->result['content'] = $this->getLogger()->error(
                ['Fetch template "{$0}" failed', $this->template],
                __FILE__,
                __LINE__,
                $e
            );

            array_shift(Render::$templates);
        }
    }

    /**
     * @param string $template
     */
    public function setTemplate($template)
    {
        $actionClass = $this->actionClass;

        if ($template === null) {
            $this->template = $actionClass;
            return;
        }

        if ($template === '') {
            $this->template = $template;
            return;
        }

        if ($template[0] == '_') {
            $this->template = $actionClass . $template;
            return;
        }

        $this->template = $template;
    }

    /**
     * @param Render $viewRenderClass
     */
    public function setViewRenderClass($viewRenderClass)
    {
        $this->viewRenderClass = Render::getClass($viewRenderClass);
    }

    /**
     * @param string $layout
     */
    public function setLayout($layout)
    {
        if ($layout === null) {
            $this->layout = 'div.' . $this->getActionName();

            return;
        }

        if ($layout === '') {
            $this->layout = $layout;
            return;
        }

        if ($layout[0] == '_') {
            $this->layout = 'div.' . $this->getActionName() . $layout;
            return;
        }

        $this->layout = $layout;
    }

    public function getActionName()
    {
        return $this->getResult()['actionName'];
    }

    public function setData($output)
    {
        $this->result['data'] = $output;
    }

    public function getData()
    {
        return $this->getResult()['data'];
    }

    public function getErrors()
    {
        return $this->getResult()['error'];
    }

    public function __toString()
    {
        try {
            return $this->getContent();
        } catch (\Exception $e) {
            return $e->getMessage() . ' (' . $e->getFile() . ':' . $e->getLine() . ')';
        }
    }

    public function setContent($content)
    {
        $this->result['content'] = $content;
    }

    public function setError($error) {
        $this->result['error'] = $error;
    }
}
