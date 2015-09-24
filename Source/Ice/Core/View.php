<?php
namespace Ice\Core;

use Ice\Action\View_Render;
use Ice\Exception\Error;
use Ice\Helper\Emmet;
use Ice\Helper\Input;
use Ice\Helper\Json;
use Ice\Helper\Object;
use Ice\Helper\String;
use Zend\Code\Generator\DocBlock\Tag\ReturnTag;

abstract class View extends Container
{
    use Stored;
    use Rendered;

    /**
     * @var string
     */
    private $template = null;

    /**
     * @var string
     */
    private $renderClass = null;

    /**
     * @var string
     */
    private $layout = null;

    private $dataParams = null;

    /**
     * @var string
     */
    private $actionClass = null;

    /**
     * @var string
     */
    private $viewClass = null;

    /**
     * @var string
     */
    private $dataUrl = null;

    /**
     * @var string
     */
    private $result = null;

    private $name = null;

    /**
     * @var Widget[]
     */
    private $widgets = [];

    /**
     * @var View[]
     */
    private $observers = [];

    /**
     * @param null $key
     * @param null $ttl
     * @return View
     */
    public static function getInstance($key = null, $ttl = null)
    {
        return parent::getInstance($key, $ttl);
    }

    public function getName()
    {
        if ($this->name !== null) {
            return $this->name;
        }

        return $this->name = crc32(String::getRandomString());
    }

    /**
     * @param View $view
     * @param $viewClass
     * @return $this
     */
    public function addObserver(View $view, $viewClass) {
        $this->observers[$view->getForViewId()] = $viewClass;

        return $this;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        if ($this->name === null) {
            $this->name = $name;
            return;
        }

        Logger::getInstance(__CLASS__)
            ->warning(
                [
                    'Name for view already defined as {$0}. Name {$1} not define',
                    [$this->name, $name]
                ],
                __FILE__,
                __LINE__
            );
    }

    /**
     * @param null $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * @param null $layout
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;
    }

    /**
     * @param null $renderClass
     */
    public function setRenderClass($renderClass)
    {
        $this->renderClass = $renderClass;
    }

    /**
     * @return string
     */
    public function getViewClass()
    {
        if ($this->viewClass !== null) {
            return $this->viewClass;
        }

        return $this->viewClass = get_class($this);
    }

    /**
     * @return string
     */
    public function getActionClass()
    {
        if ($this->actionClass !== null) {
            return $this->actionClass;
        }

        return $this->viewClass = View_Render::getClass();
    }

    /**
     * @return string
     */
    public function getDataUrl()
    {
        if ($this->dataUrl !== null) {
            return $this->dataUrl;
        }

        return $this->dataUrl = Request::uri(true);
    }

    protected static function create($key)
    {
        $class = self::getClass();

        $view = new $class();
        $view->dataParams = Input::get($class);

        return $view;
    }

    public function __toString()
    {
        try {
            return $this->render();
        } catch (\Exception $e) {
            return $e->getMessage() . ' - ' . $this->getClass() . ' (' . $e->getFile() . ':' . $e->getLine() . ')<br>' .
            nl2br($e->getTraceAsString());
        }
    }

    public function render()
    {
        if ($this->result !== null) {
            return $this->result;
        }

        $this->result = '';

        $output = (array) $this->init($this->dataParams);

        $dataParams = $this->dataParams;

        foreach ($this->widgets as $widget) {
            $dataParams = array_merge($widget->getDataParams(), $dataParams);
        }

        /** @var View $viewClass */
        $viewClass = get_class($this);

        $template = $viewClass::getTemplate($this->template);

        if (!$template) {
            foreach ($this->widgets as $widget) {
                $widget->setDataParams($dataParams);
                $this->result .= $widget->render();
            }
        } else {
            $data = [];

            foreach ($this->widgets as $widget) {
                $widget->setDataParams($dataParams);
                $data[$widget->getName()] = $widget->render();
            }

            $this->result = $viewClass::getRender($this->renderClass)
                ->fetch($template, array_merge($output, $data));
        }

        $layout = $this->getLayout(
            $this->layout,
            '[data-action="' . $this->getActionClass() .
            '" data-view="' . $this->getViewClass() .
            '" data-params="' . Json::encode($dataParams) .
            '" data-url="' . $this->getDataUrl() . '"]'
        );
//
//        require_once VENDOR_DIR . 'ifacesoft/ice/Source/artem_c/emmet/Emmet.php';
//        require_once VENDOR_DIR . 'ifacesoft/ice/Source/Ice/Helper/Emmet.php';
//
//        return $layout
//            ? $emmet->create(['viewContent' => $this->result])
//            : $this->result;
//
//        return $layout
//            ? Emmet::translate($layout . '{{$viewContent}}', ['viewContent' => $this->result])
//            : $this->result;

        return $layout
            ? '<div id="' . $this->getForViewId() . '" class="View"' .
//            ' data-url="' . $this->getDataUrl() . '"' .
//            ' data-action="' . $this->getActionClass() . '"' .
//            ' data-view="' . $this->getViewClass() . '"' .
            ' data-params=\'' . Json::encode($dataParams) . '\'' .
            ' data-observers=\'' . Json::encode($this->observers) . '\'' .
            '>' . $this->result . '</div>'
            : $this->result;
    }

    /**
     * Widget config
     *
     * @return array
     *
     *  protected static function config()
     *  {
     *      return [
     *          'render' => ['template' => null, 'class' => 'Ice:Php', 'layout' => true],
     *          'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => 'View: Access denied!'],
     *          'cache' => ['ttl' => -1, 'count' => 1000],
     *          'input' => [],
     *          'output' => []
     *      ];
     *  }
     *
     * /**
     * init widgets in view
     *
     * @param array $input
     * @return array
     */
    public abstract function init(array $input);

    /**
     * @param $widgetClass
     * @param $name
     * @return Widget
     */
    protected function initWidget($widgetClass, $name)
    {
        /** @var Widget $widgetClass */
        $widgetClass = Widget::getClass($widgetClass);

        $widget = $widgetClass::create();

        $widget
            ->setName($name)
            ->setForViewId($this->getForViewId())
            ->setResource(get_class($this))
            ->setUrl(Request::uri(true))
            ->setViewClass(get_class($this))
            ->setActionClass('Ice:View_Render');

        $widget->init($widget->getValues());

        $this->widgets[$name] = $widget;

        return $widget;
    }

    private function getForViewId() {
        return 'View_' . Object::getClassName(get_class($this)) . '_' . $this->getName();
    }

    protected function getWidgetClass($widgetClass)
    {
        if ($widgetClass[0] == '_') {
            /** @var View viewClass */
            $viewClass = get_class($this);
            $widgetClass = $viewClass::getModuleAlias() . ':' . $viewClass::getClassName() . $widgetClass;
        }

        return Widget::getClass($widgetClass);
    }
}